# 07_SECURITY.md — Arsitektur Keamanan Tracer Study UNISYA

**Versi:** 1.0.0
**Tanggal Dibuat:** 2026-06-04
**Institusi:** Universitas Islam Syarifuddin (UNISYA)
**Status:** AKTIF

---

## 1. PRINSIP KEAMANAN

Sistem Informasi Tracer Study UNISYA dirancang dengan prinsip **Defense in Depth** (Pertahanan Berlapis), di mana setiap lapisan sistem memiliki mekanisme keamanan tersendiri. Kegagalan satu lapisan tidak serta-merta mengkompromikan keseluruhan sistem.

### 1.1 Prinsip Utama

| Prinsip | Implementasi |
|---------|-------------|
| **Least Privilege** | Setiap role hanya mendapat akses minimum yang diperlukan (RBAC via Policy & Gate) |
| **Defense in Depth** | Keamanan diterapkan di lapisan middleware, service, model, dan database |
| **Secure by Default** | Konfigurasi default selalu aman; fitur berbahaya wajib diaktifkan secara eksplisit |
| **Fail Safely** | Kegagalan sistem mengarah ke state aman (deny by default) |
| **Zero Trust** | Setiap request diverifikasi ulang, tidak ada asumsi kepercayaan implisit |
| **Audit Everything** | Semua aksi sensitif dicatat di audit trail |

---

## 2. AUTENTIKASI & OTORISASI

### 2.1 Mekanisme Autentikasi

#### 2.1.1 Session Authentication (Web)
- Digunakan untuk akses langsung via browser (halaman web)
- Laravel Session Guard dengan driver `file`
- Cookie session ber-flag `HttpOnly`, `Secure`, `SameSite=Lax`
- Session ID di-regenerate setiap login sukses (`session()->regenerate()`)
- Session timeout: 120 menit tidak aktif

```php
// config/session.php
'secure'    => env('SESSION_SECURE_COOKIE', true), // HTTPS only
'http_only' => true,
'same_site' => 'lax',
'lifetime'  => 120,
```

#### 2.1.2 Sanctum API Token Authentication
- Digunakan untuk API call dari Vue SPA
- Token disimpan di `personal_access_tokens` (kolom `token` di-hash SHA-256)
- Plain token hanya dikirim sekali saat login; tidak pernah disimpan di database
- Token employer menggunakan `ability('employer')` untuk scope terbatas
- Token expired setelah 24 jam untuk alumni, 2 jam untuk employer

```php
// Membuat token untuk alumni
$token = $user->createToken('alumni-token', ['alumni'], now()->addHours(24));

// Membuat token untuk employer (scope terbatas)
$token = $user->createToken('employer-token', ['employer'], now()->addHours(2));

// Validasi ability di middleware
$request->user()->tokenCan('employer');
```

#### 2.1.3 OTP Authentication
- OTP 6 digit numerik, dibangkitkan secara kriptografis (`random_int()`)
- OTP di-hash dengan `Hash::make()` sebelum disimpan ke database
- Plain OTP hanya dikirim via WhatsApp/Email; tidak pernah disimpan
- OTP berlaku **5 menit** sejak diterbitkan
- Maksimal **5 kali percobaan** per OTP; setelah itu OTP dinonaktifkan
- Setelah digunakan, kolom `is_used = 1` dan `used_at` dicatat

```php
// OtpService.php — Generate OTP
public function generate(string $identifier, string $type, string $purpose): string
{
    $otpPlain = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    
    OtpVerification::create([
        'identifier'      => $identifier,
        'identifier_type' => $type,
        'otp_code'        => Hash::make($otpPlain), // HASHED, tidak plain
        'purpose'         => $purpose,
        'expires_at'      => now()->addMinutes(5),
        'max_attempts'    => 5,
    ]);
    
    return $otpPlain; // Hanya dikembalikan untuk dikirim via WA/Email
}

// Verifikasi OTP
public function verify(string $identifier, string $otpPlain): bool
{
    $otp = OtpVerification::where('identifier', $identifier)
        ->where('is_used', 0)
        ->where('expires_at', '>', now())
        ->latest()
        ->first();

    if (!$otp) return false;

    $otp->increment('attempts');

    if ($otp->attempts >= $otp->max_attempts) {
        $otp->update(['is_used' => 1]); // Nonaktifkan jika melebihi batas
        return false;
    }

    if (!Hash::check($otpPlain, $otp->otp_code)) return false;

    $otp->update(['is_used' => 1, 'used_at' => now()]);
    return true;
}
```

#### 2.1.4 Employer Access Token
- Token 64 karakter acak, dibangkitkan dengan `Str::random(64)`
- Disimpan dalam bentuk hash (`hash('sha256', $token)`)
- Plain token dikirim via WhatsApp/Email, kemudian kolom `token_plain` di-null-kan
- Token bersifat **single-use** (`is_used = 1` setelah terpakai)
- Token dapat **dicabut** oleh Admin (`is_revoked = 1`)
- Masa berlaku dapat dikonfigurasi (default: 72 jam)

### 2.2 Role-Based Access Control (RBAC)

#### 2.2.1 Daftar Role

| Role | Deskripsi | Akses |
|------|-----------|-------|
| `super_admin` | Administrator sistem | Full access semua modul |
| `alumni` | Pengguna alumni terdaftar | Profil sendiri, kuesioner, employer token |
| `pengguna_alumni` | Employer (via token) | Hanya portal employer + kuesioner terkait |

#### 2.2.2 Policy & Gate

Setiap model sensitif memiliki Policy class di `app/Policies/`. Semua route yang memerlukan izin menggunakan `$this->authorize()` atau middleware `can:`.

```php
// Contoh UserPolicy
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'super_admin';
    }

    public function update(User $user, User $model): bool
    {
        return $user->role === 'super_admin' 
            || $user->id === $model->id; // Alumni boleh edit profil sendiri
    }

    public function delete(User $user, User $model): bool
    {
        return $user->role === 'super_admin' && $user->id !== $model->id;
    }
}
```

#### 2.2.3 Middleware Auth

```php
// routes/api.php
Route::middleware(['auth:sanctum', 'ensure.active'])->group(function () {
    Route::middleware('can:admin')->group(function () {
        // Route admin
    });
    Route::middleware('can:alumni')->group(function () {
        // Route alumni
    });
});

Route::middleware(['employer.token'])->group(function () {
    // Route employer
});
```

---

## 3. PERLINDUNGAN INPUT & OUTPUT

### 3.1 CSRF Protection

- Laravel CSRF Protection aktif untuk semua route `web`
- Vue SPA mengirim CSRF token via header `X-XSRF-TOKEN` (dibaca dari cookie)
- API route menggunakan Sanctum token (tidak memerlukan CSRF tambahan)

```javascript
// axios.js — Konfigurasi global Axios
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
```

```php
// Verifikasi CSRF di middleware
// Sudah ditangani otomatis oleh Laravel VerifyCsrfToken middleware
```

### 3.2 XSS Protection

- **Output escaping:** Semua output ke view menggunakan Blade `{{ }}` (auto-escape)
- **Header X-Content-Type-Options:** `nosniff` — mencegah MIME sniffing
- **Header X-XSS-Protection:** `1; mode=block`
- **Content-Security-Policy (CSP):** Dikonfigurasi di SecurityHeaders middleware
- **Vue 3:** Binding `{{ }}` secara default aman; penggunaan `v-html` dilarang kecuali untuk konten yang sudah di-sanitasi

```php
// SecurityHeaders middleware
public function handle(Request $request, Closure $next): Response
{
    $response = $next($request);
    
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    $response->headers->set(
        'Content-Security-Policy',
        "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline' fonts.googleapis.com; font-src 'self' fonts.gstatic.com; img-src 'self' data:; connect-src 'self'"
    );
    
    return $response;
}
```

### 3.3 SQL Injection Prevention

- **Eloquent ORM** digunakan untuk semua query database — parameterized query secara default
- Query mentah (`DB::raw()`) **dilarang** tanpa sanitasi eksplisit
- Jika terpaksa menggunakan raw query, gunakan binding parameter:
  ```php
  DB::select('SELECT * FROM alumni WHERE graduation_year = ?', [$year]);
  ```
- Input validation ketat menggunakan Laravel Form Request di semua endpoint

### 3.4 Mass Assignment Protection

- Semua Model Eloquent wajib mendefinisikan `$fillable` secara eksplisit
- `$guarded = ['*']` digunakan sebagai fallback pada model yang memiliki banyak field sensitif
- Field sensitif (`password`, `role`, `is_active`, `created_by`) tidak boleh masuk `$fillable`

```php
// Contoh Model Alumni
protected $fillable = [
    'user_id', 'study_program_id', 'nim', 'name', 'gender',
    'birth_place', 'birth_date', 'address', 'city', 'province',
    'postal_code', 'phone', 'email', 'graduation_year', 'graduation_date',
    'ipk', 'thesis_title', 'photo', 'employment_status', 'waiting_period_months',
];

// Field yang tidak boleh di-fillable:
// is_employed, created_by, updated_by, deleted_by
```

### 3.5 File Upload Validation

Upload file (foto alumni, import Excel) divalidasi ketat:

```php
// Request validation untuk upload foto
'photo' => [
    'nullable',
    'image',                    // Hanya file gambar
    'mimes:jpeg,jpg,png,webp',  // Ekstensi yang diizinkan
    'max:2048',                 // Maksimal 2MB
    'dimensions:max_width=2000,max_height=2000', // Dimensi maks
],

// Request validation untuk import Excel
'file' => [
    'required',
    'file',
    'mimes:xlsx,xls',  // Hanya Excel
    'max:10240',       // Maksimal 10MB
],
```

- File yang diupload disimpan di `storage/app/private/` (tidak dapat diakses langsung via URL)
- Nama file di-rename dengan `Str::uuid()` untuk mencegah path traversal
- MIME type divalidasi dari konten file (bukan hanya ekstensi)

---

## 4. RATE LIMITING

### 4.1 Konfigurasi Rate Limiter

```php
// RouteServiceProvider.php / AppServiceProvider.php
RateLimiter::for('auth-login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip())
        ->response(fn() => response()->json([
            'status'  => false,
            'message' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam 1 menit.',
        ], 429));
});

RateLimiter::for('otp-request', function (Request $request) {
    return Limit::perMinute(3)->by($request->input('identifier', $request->ip()));
});

RateLimiter::for('otp-verify', function (Request $request) {
    return Limit::perMinutes(5, 5)->by($request->input('identifier', $request->ip()));
});

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

RateLimiter::for('export', function (Request $request) {
    return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
});
```

### 4.2 Tabel Rate Limit

| Endpoint Group | Limit | Window | Key |
|----------------|-------|--------|-----|
| Login (email/password) | 5 request | 1 menit | IP Address |
| OTP Request | 3 request | 1 menit | Identifier (email/phone) |
| OTP Verify | 5 request | 5 menit | Identifier |
| Employer OTP Request | 3 request | 1 menit | Token |
| API Umum (terautentikasi) | 60 request | 1 menit | User ID |
| API Umum (tidak terautentikasi) | 20 request | 1 menit | IP Address |
| Export (Excel/PDF) | 5 request | 1 menit | User ID |
| Import (Excel) | 3 request | 5 menit | User ID |

---

## 5. KEAMANAN PASSWORD

### 5.1 Hashing Password

- **Wajib menggunakan `Hash::make()`** — menggunakan bcrypt dengan cost factor 12
- Password **tidak pernah** disimpan dalam bentuk plain text
- Password **tidak pernah** dicatat di log
- Perbandingan password menggunakan `Hash::check()` yang timing-safe

```php
// Membuat password
$user->password = Hash::make($request->password);

// Memverifikasi password
if (!Hash::check($request->password, $user->password)) {
    throw new AuthenticationException('Password tidak valid.');
}

// Reset password (oleh Super Admin)
$user->update(['password' => Hash::make($newPassword)]);
```

### 5.2 Kebijakan Password

- Panjang minimal: **8 karakter**
- Wajib mengandung: huruf besar, huruf kecil, dan angka
- Tidak boleh sama dengan 3 password terakhir (opsional, tergantung kebutuhan)
- Validasi menggunakan Laravel Password rule:

```php
use Illuminate\Validation\Rules\Password;

'password' => [
    'required',
    Password::min(8)
        ->mixedCase()
        ->numbers(),
    'confirmed',
],
```

---

## 6. KEAMANAN SESSION

### 6.1 Konfigurasi Session

```php
// config/session.php
return [
    'driver'          => 'file',
    'lifetime'        => 120,        // 120 menit
    'expire_on_close' => false,
    'encrypt'         => true,       // Session di-enkripsi
    'secure'          => true,       // HTTPS only
    'http_only'       => true,       // Tidak dapat diakses JavaScript
    'same_site'       => 'lax',
];
```

### 6.2 Proteksi Session Fixation

```php
// LoginController.php — Setelah login sukses
Auth::login($user);
$request->session()->regenerate(); // Regenerate session ID
```

### 6.3 Proteksi Session Hijacking

- Cookie session hanya dikirim via HTTPS (`secure = true`)
- Flag `HttpOnly` mencegah akses JavaScript ke cookie session
- `SameSite=Lax` mencegah CSRF berbasis cookie lintas situs
- IP binding opsional (dapat dikonfigurasi via `app_settings`)

---

## 7. KEAMANAN DATABASE

### 7.1 Koneksi Database

- Database server hanya dapat diakses dari `localhost` (127.0.0.1)
- Kredensial database disimpan di `.env` (tidak di version control)
- User database memiliki privilege minimal (SELECT, INSERT, UPDATE, DELETE)
- User database tidak memiliki hak `DROP`, `CREATE`, `ALTER` di production

### 7.2 Data Sensitif di Database

| Field | Proteksi |
|-------|---------|
| `users.password` | Bcrypt hash (Hash::make) |
| `otp_verifications.otp_code` | Bcrypt hash (Hash::make) |
| `employer_access_tokens.token` | SHA-256 hash |
| `personal_access_tokens.token` | SHA-256 hash (Laravel default) |
| `employer_access_tokens.token_plain` | Di-null segera setelah pengiriman |

### 7.3 Soft Delete & Audit Fields

- Semua data master menggunakan `SoftDeletes` — data tidak pernah benar-benar terhapus
- Setiap baris menyimpan `created_by`, `updated_by`, `deleted_by` (FK ke `users.id`)
- Audit trail mencatat setiap perubahan via Model Observer

---

## 8. KEAMANAN API

### 8.1 CORS Configuration

```php
// config/cors.php
return [
    'paths'               => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods'     => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'allowed_origins'     => [env('FRONTEND_URL', 'https://tracer.unisya.ac.id')],
    'allowed_origins_patterns' => [],
    'allowed_headers'     => ['Content-Type', 'Authorization', 'X-Requested-With', 'X-XSRF-TOKEN'],
    'exposed_headers'     => [],
    'max_age'             => 3600,
    'supports_credentials' => true,
];
```

### 8.2 Request Validation

Setiap endpoint API wajib menggunakan **Form Request** yang terpisah:

```php
// Contoh: StoreAlumniRequest.php
public function rules(): array
{
    return [
        'nim'             => ['required', 'string', 'max:50', 'unique:alumni,nim'],
        'name'            => ['required', 'string', 'max:255'],
        'gender'          => ['required', 'in:laki_laki,perempuan'],
        'study_program_id'=> ['required', 'uuid', 'exists:study_programs,id'],
        'graduation_year' => ['required', 'integer', 'min:1990', 'max:' . date('Y')],
        'email'           => ['nullable', 'email', 'max:255'],
        'phone'           => ['nullable', 'string', 'max:20', 'regex:/^62[0-9]{8,13}$/'],
    ];
}

public function messages(): array
{
    return [
        'nim.required'          => 'NIM wajib diisi.',
        'nim.unique'            => 'NIM sudah terdaftar dalam sistem.',
        'gender.in'             => 'Jenis kelamin tidak valid.',
        'phone.regex'           => 'Format nomor telepon tidak valid. Gunakan format 62xxxxxxxxx.',
    ];
}
```

### 8.3 Response Security

- Response selalu dalam format JSON yang konsisten
- Pesan error tidak mengekspos detail stack trace di production (`APP_DEBUG=false`)
- ID database (UUID) aman dari enumerasi karena tidak berurutan
- Informasi sensitif (password hash, OTP hash, token hash) tidak pernah dikembalikan di response

---

## 9. KEAMANAN INTEGRASI EKSTERNAL

### 9.1 WhatsApp Gateway UNISYA

```
Endpoint : https://wacenter.unisya.ac.id/send-message
Method   : POST (JSON) / GET (Query String)
```

**Keamanan implementasi:**

```php
// WhatsAppGatewayService.php
class WhatsAppGatewayService
{
    private string $apiKey;
    private string $sender;
    private string $endpoint;

    public function __construct()
    {
        // Ambil dari database settings (terenkripsi), bukan hard-coded
        $this->apiKey   = SettingService::get('wa_gateway_api_key');
        $this->sender   = SettingService::get('wa_gateway_sender');
        $this->endpoint = config('services.wa_gateway.url');
    }

    public function send(string $number, string $message, ?string $footer = null): bool
    {
        // Sanitasi nomor telepon
        $number = $this->sanitizePhoneNumber($number);

        $payload = [
            'api_key' => $this->apiKey,
            'sender'  => $this->sender,
            'number'  => $number,
            'message' => $message,
        ];

        if ($footer) {
            $payload['footer'] = $footer;
        }

        try {
            $response = Http::timeout(10)
                ->retry(2, 500)
                ->post($this->endpoint, $payload);

            $this->logNotification($number, $message, $response->json('status'));
            return $response->json('status') === true;
        } catch (\Exception $e) {
            Log::error('WA Gateway Error: ' . $e->getMessage(), [
                'number' => substr($number, 0, 5) . '***', // Masking nomor di log
            ]);
            return false;
        }
    }

    private function sanitizePhoneNumber(string $number): string
    {
        // Hapus karakter non-numerik
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Konversi format 08xxx ke 628xxx
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }
        
        return $number;
    }
}
```

**Kebijakan keamanan WA Gateway:**
- API Key disimpan di `app_settings` (bukan `.env`) dan di-encrypt
- Nomor telepon di-masking di log (`62812***xxxx`)
- Timeout 10 detik dengan 2 kali retry
- Notifikasi diproses via Queue (tidak blocking request utama)

### 9.2 SMTP Integration

- Kredensial SMTP dikonfigurasi via Settings Management (terenkripsi di `app_settings`)
- Koneksi SMTP menggunakan TLS/SSL
- Template email tidak memuat data sensitif

---

## 10. AUDIT TRAIL & SECURITY LOGGING

### 10.1 Audit Trail System

Setiap perubahan data pada model penting dicatat otomatis via Model Observer:

```php
// AuditTrailObserver.php
class AuditTrailObserver
{
    public function created(Model $model): void
    {
        $this->log($model, 'created', null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $this->log($model, 'updated', $model->getOriginal(), $model->getChanges());
    }

    public function deleted(Model $model): void
    {
        $this->log($model, 'deleted', $model->getAttributes(), null);
    }

    private function log(Model $model, string $event, ?array $oldValues, ?array $newValues): void
    {
        // Hapus field sensitif dari log
        $sensitiveFields = ['password', 'otp_code', 'token', 'token_plain'];
        
        if ($oldValues) {
            $oldValues = array_diff_key($oldValues, array_flip($sensitiveFields));
        }
        if ($newValues) {
            $newValues = array_diff_key($newValues, array_flip($sensitiveFields));
        }

        AuditTrail::create([
            'model_type' => get_class($model),
            'model_id'   => $model->getKey(),
            'event'      => $event,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'user_id'    => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
```

**Model yang di-audit:**
- `User`, `Alumni`, `Faculty`, `StudyProgram`
- `Institution`, `InstitutionDetail`
- `Questionnaire`, `QuestionnaireQuestion`
- `TracerStudy`, `QuestionnaireResponse`
- `EmployerAccessToken`, `AlumniRequest`
- `AppSetting`

### 10.2 Security Event Logging

Event keamanan khusus yang dicatat terpisah di `activity_logs`:

| Event | Keterangan |
|-------|-----------|
| `login_success` | Login berhasil (user_id, IP, user_agent) |
| `login_failed` | Login gagal (identifier, IP, alasan) |
| `otp_requested` | OTP diminta (identifier, tujuan, IP) |
| `otp_verified` | OTP berhasil diverifikasi |
| `otp_failed` | OTP gagal diverifikasi (attempts, IP) |
| `otp_expired` | OTP kadaluarsa saat diverifikasi |
| `employer_token_created` | Token employer dibuat |
| `employer_token_used` | Token employer digunakan pertama kali |
| `employer_token_revoked` | Token employer dicabut admin |
| `password_reset` | Password di-reset oleh admin |
| `account_deactivated` | Akun dinonaktifkan |
| `unauthorized_access` | Akses ditolak (403) |
| `rate_limit_exceeded` | Rate limit terlampaui (429) |
| `file_upload` | Upload file (tipe, ukuran, hasil validasi) |
| `data_export` | Export data (tipe, filter, user) |

### 10.3 Log Security

- Log aplikasi disimpan di `storage/logs/laravel-{date}.log`
- Log sensitif di-masking (password, token, nomor telepon)
- Log dirotasi harian (`LOG_CHANNEL=daily`)
- Di production, log level minimal adalah `warning`

```php
// config/logging.php
'default' => env('LOG_CHANNEL', 'daily'),
'channels' => [
    'daily' => [
        'driver' => 'daily',
        'path'   => storage_path('logs/laravel.log'),
        'level'  => env('LOG_LEVEL', 'warning'), // 'debug' untuk development
        'days'   => 14, // Simpan 14 hari
    ],
],
```

---

## 11. KEAMANAN SERVER & DEPLOYMENT

### 11.1 Konfigurasi Production

```env
# Wajib di production
APP_ENV=production
APP_DEBUG=false          # JANGAN PERNAH true di production
APP_KEY=base64:...       # Kunci enkripsi 32 byte, wajib ada

# Session & Cookie
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true
```

### 11.2 File & Direktori Permissions

```bash
# Permissions yang benar untuk production
find /path/to/project -type f -exec chmod 644 {} \;
find /path/to/project -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Lindungi file sensitif
chmod 600 .env
```

### 11.3 File .env Protection

- `.env` tidak pernah masuk ke version control (ada di `.gitignore`)
- `.env.example` berisi template tanpa nilai sensitif
- Di server, `.env` hanya dapat dibaca oleh user web server

### 11.4 Nginx Security Headers

```nginx
server {
    # Nonaktifkan server version disclosure
    server_tokens off;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    # Lindungi file sensitif
    location ~ /\. {
        deny all;
    }
    
    location ~* \.(env|log|git)$ {
        deny all;
    }
    
    # Hanya izinkan akses ke /public
    root /www/wwwroot/tracer.unisya.ac.id/public;
}
```

### 11.5 PHP Security (php.ini)

```ini
; Sembunyikan versi PHP
expose_php = Off

; Batasi upload file
upload_max_filesize = 10M
post_max_size = 12M

; Nonaktifkan fungsi berbahaya
disable_functions = exec,passthru,shell_exec,system,proc_open,popen

; Session security
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
```

---

## 12. CHECKLIST KEAMANAN PER FASE

### Sebelum Setiap Sesi Development

- [ ] `APP_DEBUG=false` di production
- [ ] Tidak ada kredensial hard-coded dalam kode
- [ ] Semua endpoint baru menggunakan Form Request
- [ ] Semua endpoint baru memiliki Policy check
- [ ] Semua endpoint baru memiliki Rate Limiter
- [ ] Password menggunakan `Hash::make()` — tidak ada plain text
- [ ] OTP menggunakan `Hash::make()` — tidak ada plain text
- [ ] Token menggunakan hash SHA-256 — tidak ada plain text
- [ ] File upload divalidasi tipe dan ukurannya
- [ ] Log tidak memuat data sensitif
- [ ] Audit trail terpasang untuk model baru

### Sebelum Deployment Production

- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `php artisan config:cache` dan `php artisan route:cache`
- [ ] Permission file dan direktori sudah benar
- [ ] `.env` tidak dapat diakses publik
- [ ] SSL/TLS aktif dan valid
- [ ] Semua security headers terpasang di Nginx/Apache
- [ ] Queue worker aktif via Supervisor
- [ ] Cron job `schedule:run` aktif
- [ ] Backup database terjadwal

---

## 13. REFERENSI STANDAR

| Standar | Relevansi |
|---------|-----------|
| OWASP Top 10 2021 | Panduan utama keamanan aplikasi web |
| OWASP ASVS Level 2 | Application Security Verification Standard |
| NIST SP 800-63B | Panduan autentikasi digital (OTP, password) |
| Permenristekdikti No. 62/2016 | SPMI Perguruan Tinggi (konteks keamanan data akademik) |
| UU PDP No. 27/2022 | Perlindungan Data Pribadi — relevan untuk data alumni |

---

*Dokumen ini adalah referensi keamanan permanen proyek. Setiap perubahan keputusan keamanan harus dicatat di 09_CHANGELOG.md.*
