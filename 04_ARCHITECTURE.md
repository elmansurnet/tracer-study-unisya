# 04_ARCHITECTURE.md — Arsitektur Sistem Tracer Study UNISYA

**Versi:** 1.0.0
**Tanggal Dibuat:** 2026-06-04

---

## 1. ARSITEKTUR KESELURUHAN

```
┌─────────────────────────────────────────────────────────┐
│                    CLIENT LAYER                          │
│  ┌──────────────────────────────────────────────────┐   │
│  │   Vue 3 SPA (Vite + Tailwind + Pinia)            │   │
│  │   - Alumni Dashboard                             │   │
│  │   - Admin Dashboard                              │   │
│  │   - Employer Portal                              │   │
│  └──────────────────┬───────────────────────────────┘   │
└─────────────────────│───────────────────────────────────┘
                       │ HTTPS / Axios
┌─────────────────────▼───────────────────────────────────┐
│                   WEB SERVER LAYER                       │
│   Nginx / Apache (Ubuntu + aaPanel)                      │
│   - SSL Termination                                      │
│   - Static Asset Serving                                 │
│   - PHP-FPM Proxy                                        │
└─────────────────────┬───────────────────────────────────┘
                       │
┌─────────────────────▼───────────────────────────────────┐
│                  APPLICATION LAYER                       │
│   Laravel 12 (PHP 8.3)                                   │
│                                                          │
│  ┌──────────┐  ┌──────────┐  ┌──────────────────────┐   │
│  │ Web      │  │ API      │  │ Console / Scheduler   │   │
│  │ Routes   │  │ Routes   │  │ (Artisan Commands)    │   │
│  └────┬─────┘  └────┬─────┘  └──────────┬───────────┘   │
│       │              │                   │               │
│  ┌────▼──────────────▼───────────────────▼────────────┐  │
│  │              MIDDLEWARE STACK                       │  │
│  │  Auth, CSRF, RateLimit, CORS, XSS, Audit           │  │
│  └────────────────────┬────────────────────────────────┘  │
│                       │                                  │
│  ┌────────────────────▼────────────────────────────────┐  │
│  │               CONTROLLER LAYER                      │  │
│  │  HTTP Controllers (thin — hanya routing ke service) │  │
│  └────────────────────┬────────────────────────────────┘  │
│                       │                                  │
│  ┌────────────────────▼────────────────────────────────┐  │
│  │               SERVICE LAYER                         │  │
│  │  Business Logic (AuthService, AlumniService,        │  │
│  │  QuestionnaireService, NotificationService,         │  │
│  │  ReportService, WhatsAppService, OtpService...)     │  │
│  └────────────────────┬────────────────────────────────┘  │
│                       │                                  │
│  ┌────────────────────▼────────────────────────────────┐  │
│  │               REPOSITORY LAYER                      │  │
│  │  Data Access (EloquentRepository pattern)           │  │
│  │  Decouples Service dari ORM specifics               │  │
│  └────────────────────┬────────────────────────────────┘  │
│                       │                                  │
│  ┌────────────────────▼────────────────────────────────┐  │
│  │               MODEL LAYER (Eloquent)                │  │
│  │  Models + Relationships + Scopes + Casts            │  │
│  └────────────────────┬────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
                       │
┌─────────────────────▼───────────────────────────────────┐
│                    DATA LAYER                            │
│   MySQL 8.0+ (InnoDB, utf8mb4)                           │
│   - Primary Data                                         │
│   - Queue Jobs                                           │
└──────────────────────────────────────────────────────────┘
                       │
┌─────────────────────▼───────────────────────────────────┐
│               BACKGROUND SERVICES                        │
│  ┌─────────────────┐    ┌────────────────────────────┐   │
│  │ Queue Worker    │    │ Laravel Scheduler           │   │
│  │ (Supervisor)    │    │ (Crontab → Artisan)         │   │
│  │ - Notifications │    │ - Reminder Notifications    │   │
│  │ - Email/WA Send │    │ - Token Cleanup             │   │
│  │ - Reports       │    │ - OTP Cleanup               │   │
│  └─────────────────┘    └────────────────────────────┘   │
└──────────────────────────────────────────────────────────┘
                       │
┌─────────────────────▼───────────────────────────────────┐
│               EXTERNAL SERVICES                          │
│  ┌──────────────────────────────────────────────────┐   │
│  │  WA Gateway UNISYA                               │   │
│  │  https://wacenter.unisya.ac.id/send-message      │   │
│  │  Method: POST (JSON) / GET (Query String)         │   │
│  └──────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────┐   │
│  │  SMTP Server (konfigurasi dinamis)               │   │
│  └──────────────────────────────────────────────────┘   │
└──────────────────────────────────────────────────────────┘
```

---

## 2. STRUKTUR DIREKTORI PROYEK

```
tracer-study-unisya/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── CleanupExpiredOtp.php
│   │       ├── CleanupExpiredTokens.php
│   │       └── SendTracerStudyReminders.php
│   ├── Exceptions/
│   │   └── Handler.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── Auth/
│   │   │   │   │   ├── LoginController.php
│   │   │   │   │   ├── OtpController.php
│   │   │   │   │   └── EmployerAccessController.php
│   │   │   │   ├── Admin/
│   │   │   │   │   ├── UserController.php
│   │   │   │   │   ├── FacultyController.php
│   │   │   │   │   ├── StudyProgramController.php
│   │   │   │   │   ├── ProfessionCategoryController.php
│   │   │   │   │   ├── ProfessionController.php
│   │   │   │   │   ├── InstitutionController.php
│   │   │   │   │   ├── InstitutionDetailController.php
│   │   │   │   │   ├── AlumniController.php
│   │   │   │   │   ├── AlumniRequestController.php
│   │   │   │   │   ├── QuestionnaireCategoryController.php
│   │   │   │   │   ├── QuestionnaireController.php
│   │   │   │   │   ├── AnswerTypeController.php
│   │   │   │   │   ├── TracerStudyController.php
│   │   │   │   │   ├── ReportController.php
│   │   │   │   │   ├── SettingController.php
│   │   │   │   │   ├── AuditTrailController.php
│   │   │   │   │   └── ActivityLogController.php
│   │   │   │   ├── Alumni/
│   │   │   │   │   ├── ProfileController.php
│   │   │   │   │   ├── EmploymentController.php
│   │   │   │   │   ├── EmployerTokenController.php
│   │   │   │   │   └── QuestionnaireResponseController.php
│   │   │   │   └── Employer/
│   │   │   │       ├── DashboardController.php
│   │   │   │       └── QuestionnaireResponseController.php
│   │   │   └── Web/
│   │   │       └── SpaController.php
│   │   ├── Middleware/
│   │   │   ├── AuditRequest.php
│   │   │   ├── CheckEmployerToken.php
│   │   │   ├── EnsureActiveUser.php
│   │   │   └── SecurityHeaders.php
│   │   └── Requests/
│   │       ├── Auth/
│   │       ├── Alumni/
│   │       ├── Admin/
│   │       └── Employer/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Alumni.php
│   │   ├── Faculty.php
│   │   ├── StudyProgram.php
│   │   ├── ProfessionCategory.php
│   │   ├── Profession.php
│   │   ├── Institution.php
│   │   ├── InstitutionDetail.php
│   │   ├── AlumniEmploymentHistory.php
│   │   ├── AlumniRequest.php
│   │   ├── EmployerAccessToken.php
│   │   ├── OtpVerification.php
│   │   ├── QuestionnaireCategory.php
│   │   ├── AnswerType.php
│   │   ├── Questionnaire.php
│   │   ├── QuestionnaireQuestion.php
│   │   ├── TracerStudy.php
│   │   ├── TracerStudyQuestionnaire.php
│   │   ├── QuestionnaireResponse.php
│   │   ├── QuestionnaireAnswer.php
│   │   ├── NotificationLog.php
│   │   ├── AppSetting.php
│   │   ├── AuditTrail.php
│   │   └── ActivityLog.php
│   ├── Notifications/
│   │   ├── OtpLoginNotification.php
│   │   ├── EmployerInvitationNotification.php
│   │   ├── TracerStudyInvitationNotification.php
│   │   └── TracerStudyReminderNotification.php
│   ├── Policies/
│   │   ├── UserPolicy.php
│   │   ├── AlumniPolicy.php
│   │   ├── TracerStudyPolicy.php
│   │   └── ... (per model)
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── AuthServiceProvider.php
│   ├── Repositories/
│   │   ├── Contracts/
│   │   └── Eloquent/
│   │       ├── AlumniRepository.php
│   │       ├── TracerStudyRepository.php
│   │       └── ...
│   └── Services/
│       ├── AuthService.php
│       ├── OtpService.php
│       ├── EmployerAccessService.php
│       ├── AlumniService.php
│       ├── TracerStudyService.php
│       ├── QuestionnaireService.php
│       ├── NotificationService.php
│       ├── WhatsAppGatewayService.php
│       ├── ReportService.php
│       ├── ExportService.php
│       ├── AuditService.php
│       └── SettingService.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── js/
│   │   ├── app.js
│   │   ├── router/
│   │   ├── stores/
│   │   ├── composables/
│   │   ├── components/
│   │   └── pages/
│   └── views/
│       └── app.blade.php
├── routes/
│   ├── web.php
│   ├── api.php
│   └── channels.php
└── ... (standard Laravel structure)
```

---

## 3. POLA DESAIN YANG DIGUNAKAN

### 3.1 Repository Pattern
- Interface di `app/Repositories/Contracts/`
- Implementasi Eloquent di `app/Repositories/Eloquent/`
- Memisahkan logika akses data dari service

### 3.2 Service Layer Pattern
- Business logic di `app/Services/`
- Controller hanya menerima request, memanggil service, mengembalikan response
- Service memanggil repository untuk akses data

### 3.3 Policy & Gate (RBAC)
- Setiap model memiliki Policy class
- Gate didaftarkan di AuthServiceProvider
- Middleware `authorize` pada setiap route yang memerlukan izin spesifik

### 3.4 Immutable Snapshot
- `questionnaire_responses.questionnaire_snapshot` — JSON snapshot saat respons dibuat
- `questionnaire_answers.question_snapshot` — JSON snapshot pertanyaan
- `questionnaire_answers.answer_type_snapshot` — JSON snapshot konfigurasi tipe jawaban

### 3.5 Queue & Jobs
- Semua pengiriman notifikasi (WA, Email) diproses via Queue
- Driver: database (untuk kemudahan deployment tanpa Redis)
- Worker dijalankan dengan Supervisor

### 3.6 Audit Trail via Observer
- Model Observer mencatat setiap `created`, `updated`, `deleted` ke `audit_trails`
- Implementasi di `app/Observers/`

---

## 4. ALUR AUTENTIKASI

### 4.1 Session Auth (Web)
```
POST /login → LoginController → AuthService
  → Validasi kredensial → Auth::attempt()
  → Buat session → Kembalikan user data
  → Vue Router redirect ke dashboard
```

### 4.2 Sanctum Token Auth (API)
```
POST /api/login → Validasi → Auth::attempt()
  → createToken() → Kembalikan plain text token
  → Client simpan token → Kirim di header: Authorization: Bearer {token}
```

### 4.3 OTP Auth
```
POST /api/auth/otp/request
  → OtpService::generate() → Simpan hashed OTP di otp_verifications
  → NotificationService::sendOtp() → Queue Job → WA/Email

POST /api/auth/otp/verify
  → OtpService::verify() → Cek hash, expiry, attempts
  → Jika valid: buat session/token → Tandai OTP sebagai used
  → Kembalikan auth data
```

### 4.4 Employer Token Auth
```
[Alumni] POST /api/alumni/employer-tokens → EmployerAccessService::createToken()
  → Buat token unik, simpan hashed di employer_access_tokens
  → Queue: kirim token plain via WA/Email ke employer

[Employer] GET /employer/access/{token} → Validasi token
  → POST /api/employer/otp/request → Kirim OTP ke kontak employer
  → POST /api/employer/otp/verify → Verifikasi OTP
  → Buat Sanctum token dengan ability 'employer' → Akses dashboard
```

---

## 5. DEPLOYMENT ARCHITECTURE (Non-Docker)

```
Ubuntu 22.04 Server
├── aaPanel
│   ├── Nginx / Apache (Virtual Host)
│   │   ├── SSL: Let's Encrypt
│   │   ├── root: /www/wwwroot/tracer.unisya.ac.id/public
│   │   └── PHP-FPM 8.3
│   ├── MySQL 8.0
│   │   └── Database: tracer_study_unisya
│   └── PHP 8.3 + Extensions:
│       └── mbstring, xml, curl, zip, gd, pdo_mysql, bcmath, openssl
├── Supervisor
│   └── laravel-worker: php artisan queue:work --sleep=3 --tries=3
└── Crontab
    └── * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

### Konfigurasi .env Minimal
```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tracer_study_unisya
DB_USERNAME=...
DB_PASSWORD=...

QUEUE_CONNECTION=database
SESSION_DRIVER=file
CACHE_DRIVER=file

# WA Gateway UNISYA
WA_GATEWAY_URL=https://wacenter.unisya.ac.id/send-message
WA_GATEWAY_API_KEY=...
WA_GATEWAY_SENDER=62888xxxx

# SMTP (dikonfigurasi via Settings Management)
MAIL_MAILER=smtp
```