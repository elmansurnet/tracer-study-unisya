# 01_BLUEPRINT.md — Sistem Informasi Tracer Study UNISYA

**Versi:** 1.0.0
**Tanggal Dibuat:** 2026-06-04
**Institusi:** Universitas Islam Syarifuddin (UNISYA)
**Status:** DRAFT — Menunggu Persetujuan

---

## 1. RINGKASAN SISTEM

Sistem Informasi Tracer Study UNISYA adalah aplikasi web berbasis Laravel 12 + Vue 3 yang dirancang untuk melacak perkembangan karier alumni Universitas Islam Syarifuddin (UNISYA), mengumpulkan data umpan balik dari pengguna alumni (employer), serta menghasilkan laporan analitik untuk kepentingan evaluasi mutu pendidikan dan akreditasi.

### 1.1 Latar Belakang

Tracer Study merupakan kewajiban perguruan tinggi untuk memantau kondisi alumni pasca kelulusan, mencakup masa tunggu kerja, relevansi pekerjaan dengan bidang studi, dan tingkat kepuasan pengguna alumni. Data ini diperlukan untuk:

- Pemenuhan borang akreditasi BAN-PT / LAM
- Evaluasi kurikulum berbasis kebutuhan industri
- Perencanaan strategis pengembangan program studi
- Pemetaan jaringan alumni dan industri

### 1.2 Tujuan Sistem

1. Menyediakan platform digital terintegrasi untuk tracer study alumni UNISYA
2. Mengotomasi pengiriman kuesioner kepada alumni dan pengguna alumni
3. Menyajikan dashboard analitik real-time untuk manajemen universitas
4. Menghasilkan laporan terstandar untuk keperluan akreditasi

---

## 2. SPESIFIKASI KEBUTUHAN FUNGSIONAL

### 2.1 Modul Autentikasi & Otorisasi

| ID | Kebutuhan | Prioritas |
|----|-----------|-----------|
| F-AUTH-01 | Alumni dapat login dengan email dan password | HARUS |
| F-AUTH-02 | Alumni dapat login via OTP WhatsApp | HARUS |
| F-AUTH-03 | Alumni dapat login via OTP Email | HARUS |
| F-AUTH-04 | Super Admin dapat login dengan email dan password | HARUS |
| F-AUTH-05 | Pengguna Alumni mendapat akses via token undangan | HARUS |
| F-AUTH-06 | Sistem mendukung verifikasi OTP dengan batas waktu | HARUS |
| F-AUTH-07 | OTP dibatasi maksimal percobaan | HARUS |
| F-AUTH-08 | Token Pengguna Alumni bersifat sekali pakai | HARUS |
| F-AUTH-09 | Token Pengguna Alumni dapat dicabut oleh Admin | HARUS |
| F-AUTH-10 | Sesi login otomatis berakhir setelah tidak aktif | HARUS |

### 2.2 Modul Manajemen Pengguna

| ID | Kebutuhan | Prioritas |
|----|-----------|-----------|
| F-USER-01 | Super Admin dapat membuat, memperbarui, menonaktifkan akun alumni | HARUS |
| F-USER-02 | Super Admin dapat melihat daftar semua pengguna | HARUS |
| F-USER-03 | Alumni dapat memperbarui profil diri | HARUS |
| F-USER-04 | Alumni dapat mengganti password | HARUS |
| F-USER-05 | Sistem mengirim notifikasi aktivasi akun ke alumni baru | HARUS |
| F-USER-06 | Super Admin dapat mengatur ulang password alumni | HARUS |
| F-USER-07 | Sistem mencatat riwayat aktivitas pengguna | HARUS |

### 2.3 Modul Manajemen Akademik

| ID | Kebutuhan | Prioritas |
|----|-----------|-----------|
| F-AKAD-01 | Super Admin dapat mengelola data Fakultas | HARUS |
| F-AKAD-02 | Super Admin dapat mengelola data Program Studi | HARUS |
| F-AKAD-03 | Program Studi terhubung dengan Fakultas | HARUS |
| F-AKAD-04 | Fakultas dan Program Studi mendukung soft delete | HARUS |

### 2.4 Modul Manajemen Profesi & Institusi

| ID | Kebutuhan | Prioritas |
|----|-----------|-----------|
| F-PROF-01 | Super Admin dapat mengelola Kategori Profesi | HARUS |
| F-PROF-02 | Super Admin dapat mengelola Profesi (terhubung ke kategori) | HARUS |
| F-PROF-03 | Super Admin dapat mengelola data Institusi/Perusahaan | HARUS |
| F-PROF-04 | Super Admin dapat mengelola Detail Institusi | HARUS |

### 2.5 Modul Manajemen Alumni

| ID | Kebutuhan | Prioritas |
|----|-----------|-----------|
| F-ALU-01 | Super Admin dapat mengelola data lengkap alumni | HARUS |
| F-ALU-02 | Alumni dapat memperbarui data pekerjaan saat ini | HARUS |
| F-ALU-03 | Sistem mencatat riwayat pekerjaan alumni | HARUS |
| F-ALU-04 | Alumni dapat mengajukan permohonan update data akademik | HARUS |
| F-ALU-05 | Super Admin dapat menyetujui/menolak permohonan alumni | HARUS |
| F-ALU-06 | Alumni dapat memilih employer/perusahaan tempat bekerja | HARUS |
| F-ALU-07 | Sistem mencatat data masa tunggu kerja alumni | HARUS |
| F-ALU-08 | Sistem dapat mengimpor data alumni dari Excel | HARUS |
| F-ALU-09 | Alumni dapat diekspor ke format Excel dan PDF | HARUS |

### 2.6 Modul Kuesioner

| ID | Kebutuhan | Prioritas |
|----|-----------|-----------|
| F-KUES-01 | Super Admin dapat mengelola Kategori Kuesioner | HARUS |
| F-KUES-02 | Super Admin dapat membuat dan mengelola Kuesioner | HARUS |
| F-KUES-03 | Kuesioner dapat ditujukan kepada Alumni atau Pengguna Alumni | HARUS |
| F-KUES-04 | Kuesioner mendukung ruang lingkup Global, Fakultas, Program Studi | HARUS |
| F-KUES-05 | Tipe jawaban mendukung Benar/Salah, Skala 1-5, Skala 1-10 | HARUS |
| F-KUES-06 | Jawaban kuesioner disimpan sebagai snapshot tidak dapat diubah | HARUS |
| F-KUES-07 | Perubahan kuesioner tidak mempengaruhi data jawaban historis | HARUS |
| F-KUES-08 | Alumni dan Pengguna Alumni dapat mengisi kuesioner | HARUS |
| F-KUES-09 | Sistem mencegah pengisian kuesioner duplikat | HARUS |
| F-KUES-10 | Kuesioner memiliki periode aktif (tanggal mulai dan berakhir) | HARUS |

### 2.7 Modul Tracer Study

| ID | Kebutuhan | Prioritas |
|----|-----------|-----------|
| F-TS-01 | Super Admin dapat membuat sesi Tracer Study | HARUS |
| F-TS-02 | Tracer Study terhubung dengan kumpulan kuesioner | HARUS |
| F-TS-03 | Sistem mengirim undangan otomatis ke alumni | HARUS |
| F-TS-04 | Sistem mengirim token akses ke employer | HARUS |
| F-TS-05 | Super Admin dapat memantau tingkat respons | HARUS |
| F-TS-06 | Tracer Study memiliki periode aktif | HARUS |

### 2.8 Modul Pelacakan Pekerjaan

| ID | Kebutuhan | Prioritas |
|----|-----------|-----------|
| F-EMP-01 | Sistem mencatat data pekerjaan alumni (employment tracking) | HARUS |
| F-EMP-02 | Sistem mencatat data employer yang mempekerjakan alumni (employer tracking) | HARUS |
| F-EMP-03 | Alumni dapat memilih employer dan menginvitasi mengisi kuesioner | HARUS |
| F-EMP-04 | Employer mendapat akses dashboard via token unik | HARUS |

### 2.9 Modul Notifikasi

| ID | Kebutuhan | Prioritas |
|----|-----------|-----------|
| F-NOTIF-01 | Sistem mengirim OTP via WhatsApp (WA Gateway UNISYA) | HARUS |
| F-NOTIF-02 | Sistem mengirim OTP via Email (SMTP) | HARUS |
| F-NOTIF-03 | Sistem mengirim undangan Tracer Study via WhatsApp | HARUS |
| F-NOTIF-04 | Sistem mengirim undangan Tracer Study via Email | HARUS |
| F-NOTIF-05 | Sistem mengirim notifikasi pengingat kepada alumni | HARUS |
| F-NOTIF-06 | Admin dapat memantau status pengiriman notifikasi | HARUS |
| F-NOTIF-07 | Notifikasi diproses menggunakan Laravel Queue | HARUS |

### 2.10 Modul Pelaporan & Analitik

| ID | Kebutuhan | Prioritas |
|----|-----------|-----------|
| F-REP-01 | Dashboard analitik dengan visualisasi ApexCharts | HARUS |
| F-REP-02 | Laporan distribusi alumni per fakultas/program studi | HARUS |
| F-REP-03 | Laporan analitik pekerjaan alumni | HARUS |
| F-REP-04 | Laporan masa tunggu kerja alumni | HARUS |
| F-REP-05 | Laporan employer/pengguna alumni | HARUS |
| F-REP-06 | Laporan hasil kuesioner | HARUS |
| F-REP-07 | Filter laporan berdasarkan rentang tanggal | HARUS |
| F-REP-08 | Ekspor laporan ke format Excel (A4, F4) | HARUS |
| F-REP-09 | Ekspor laporan ke format PDF (A4, F4) | HARUS |

### 2.11 Modul Pengaturan Sistem

| ID | Kebutuhan | Prioritas |
|----|-----------|-----------|
| F-SET-01 | Super Admin dapat mengatur konfigurasi WA Gateway | HARUS |
| F-SET-02 | Super Admin dapat mengatur konfigurasi SMTP | HARUS |
| F-SET-03 | Super Admin dapat mengatur profil institusi UNISYA | HARUS |
| F-SET-04 | Super Admin dapat melihat Audit Trail sistem | HARUS |
| F-SET-05 | Super Admin dapat melihat Activity Log | HARUS |

---

## 3. SPESIFIKASI KEBUTUHAN NON-FUNGSIONAL

### 3.1 Performa

| ID | Kebutuhan | Target |
|----|-----------|--------|
| NF-PERF-01 | Waktu respons API untuk operasi CRUD biasa | < 500ms |
| NF-PERF-02 | Waktu loading halaman dashboard | < 3 detik |
| NF-PERF-03 | Waktu proses ekspor laporan Excel/PDF | < 30 detik |
| NF-PERF-04 | Pemrosesan notifikasi massal via queue | Async, tidak memblokir UI |
| NF-PERF-05 | Sistem mendukung minimal 500 pengguna bersamaan | Baseline |

### 3.2 Keamanan

| ID | Kebutuhan | Implementasi |
|----|-----------|--------------|
| NF-SEC-01 | Perlindungan CSRF | Laravel CSRF Token |
| NF-SEC-02 | Perlindungan XSS | Escape output, Content Security Policy |
| NF-SEC-03 | Pencegahan SQL Injection | Eloquent ORM, Query Builder Binding |
| NF-SEC-04 | Rate Limiting | Laravel Rate Limiter per route |
| NF-SEC-05 | Password hashing | Hash::make() bcrypt |
| NF-SEC-06 | OTP kadaluarsa | 5 menit setelah dikirim |
| NF-SEC-07 | Batas percobaan OTP | Maksimal 5 kali |
| NF-SEC-08 | Validasi upload file | MIME type, ukuran, ekstensi |
| NF-SEC-09 | Token akses employer | Single-use, dapat dicabut |
| NF-SEC-10 | Audit trail lengkap | Setiap operasi CRUD dicatat |

### 3.3 Ketersediaan & Reliabilitas

| ID | Kebutuhan | Target |
|----|-----------|--------|
| NF-AVAIL-01 | Uptime sistem | 99.5% |
| NF-AVAIL-02 | Backup database otomatis | Harian |
| NF-AVAIL-03 | Queue worker restart otomatis | Supervisor |
| NF-AVAIL-04 | Penanganan error graceful | Error logging Sentry/log file |

### 3.4 Skalabilitas

| ID | Kebutuhan | Keterangan |
|----|-----------|------------|
| NF-SCAL-01 | Arsitektur modular dan bisa dikembangkan | Service Layer Pattern |
| NF-SCAL-02 | Database dioptimasi dengan indexing | Query < 100ms untuk data < 100k baris |
| NF-SCAL-03 | Cache untuk query berat | Laravel Cache (file/redis) |
| NF-SCAL-04 | Notifikasi massal tidak memblokir sistem | Laravel Queue |

### 3.5 Keterpeliharaan

| ID | Kebutuhan | Keterangan |
|----|-----------|------------|
| NF-MAINT-01 | Kode mengikuti PSR-12 coding standard | PHP |
| NF-MAINT-02 | Dokumentasi API lengkap | OpenAPI/Swagger |
| NF-MAINT-03 | Unit test coverage minimal 70% | PHPUnit / Pest |
| NF-MAINT-04 | Semua teks UI dalam Bahasa Indonesia profesional | — |

### 3.6 Kompatibilitas

| ID | Kebutuhan | Keterangan |
|----|-----------|------------|
| NF-COMPAT-01 | Browser modern (Chrome, Firefox, Edge, Safari) | Versi 2 tahun terakhir |
| NF-COMPAT-02 | Responsive mobile (min 375px) dan desktop | — |
| NF-COMPAT-03 | Deployment tanpa Docker | Ubuntu, aaPanel, Apache/Nginx |

---

## 4. MATRIKS PERAN PENGGUNA (RBAC)

### 4.1 Definisi Peran

| Peran | Deskripsi |
|-------|-----------|
| **Super Admin** | Administrator penuh sistem. Mengelola semua data master, konfigurasi, laporan, dan pengguna. |
| **Alumni** | Lulusan UNISYA yang terdaftar. Mengisi profil, kuesioner, dan menginvitasi employer. |
| **Pengguna Alumni** | Employer/perusahaan yang mempekerjakan alumni. Akses via token undangan tanpa registrasi. |

### 4.2 Matriks Izin Akses

| Modul / Fitur | Super Admin | Alumni | Pengguna Alumni |
|---------------|:-----------:|:------:|:---------------:|
| **Manajemen Pengguna** | CRUD | Profil sendiri | — |
| **Manajemen Fakultas** | CRUD | Lihat | — |
| **Manajemen Program Studi** | CRUD | Lihat | — |
| **Kategori Profesi** | CRUD | Lihat | — |
| **Profesi** | CRUD | Lihat | — |
| **Institusi** | CRUD | Lihat | — |
| **Detail Institusi** | CRUD | Lihat | — |
| **Data Alumni** | CRUD Semua | CRUD sendiri | — |
| **Permohonan Alumni** | Approve/Reject | Buat/Lihat milik sendiri | — |
| **Tracking Pekerjaan** | CRUD Semua | CRUD milik sendiri | — |
| **Tracking Employer** | Lihat Semua | Buat undangan | Lihat milik sendiri |
| **Kategori Kuesioner** | CRUD | Lihat | — |
| **Kuesioner** | CRUD | Isi (jika alumni) | Isi (jika employer) |
| **Tipe Jawaban** | CRUD | — | — |
| **Tracer Study** | CRUD | Lihat undangan | Lihat undangan |
| **Jawaban Kuesioner** | Lihat Semua | Lihat milik sendiri | Lihat milik sendiri |
| **Dashboard Analytics** | Penuh | Terbatas | Terbatas |
| **Laporan** | Semua | — | — |
| **Ekspor Data** | Ya | — | — |
| **Notifikasi** | Kelola | Terima | Terima |
| **Pengaturan Sistem** | Penuh | — | — |
| **Audit Trail** | Lihat | — | — |
| **Activity Log** | Lihat | — | — |

---

## 5. TEKNOLOGI STACK

### 5.1 Backend

| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| Framework | Laravel | 12.x |
| Runtime | PHP | 8.3+ |
| Database | MySQL | 8.0+ |
| Autentikasi API | Laravel Sanctum | 4.x |
| Otorisasi | Laravel Policy & Gate | — |
| Queue | Laravel Queue (Database Driver) | — |
| Scheduler | Laravel Scheduler | — |
| Notifikasi | Laravel Notifications | — |
| Export Excel | Laravel Excel (Maatwebsite) | 3.x |
| Export PDF | DomPDF (barryvdh) | 3.x |

### 5.2 Frontend

| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| Framework | Vue.js | 3.x (Composition API) |
| Build Tool | Vite | 5.x |
| CSS Framework | Tailwind CSS | 3.x |
| State Management | Pinia | 2.x |
| Router | Vue Router | 4.x |
| HTTP Client | Axios | 1.x |
| Chart Library | ApexCharts (vue3-apexcharts) | 1.x |

### 5.3 Integrasi Eksternal

| Layanan | Keterangan |
|---------|------------|
| WA Gateway UNISYA | `https://wacenter.unisya.ac.id/send-message` (POST/GET) |
| SMTP | Konfigurasi dinamis via Settings |

### 5.4 Infrastruktur

| Komponen | Keterangan |
|----------|------------|
| OS | Linux Ubuntu 20.04/22.04 |
| Panel | aaPanel |
| Web Server | Apache atau Nginx |
| Process Manager | Supervisor (untuk Queue Worker) |
| Cron | Crontab (untuk Laravel Scheduler) |

---

## 6. ALUR SISTEM UTAMA

### 6.1 Alur Pendaftaran & Login Alumni

```
[Alumni] → Kunjungi halaman login
         → Masukkan email & password  ─→ Verifikasi → Session aktif → Dashboard Alumni
         → Pilih OTP WhatsApp         ─→ Input nomor → Kirim OTP WA → Verifikasi → Session
         → Pilih OTP Email            ─→ Input email → Kirim OTP   → Verifikasi → Session
```

### 6.2 Alur Akses Pengguna Alumni (Employer)

```
[Alumni] → Pilih Employer dari daftar institusi
         → Klik "Undang Employer"
         → Sistem: buat token unik + kirim via WA/Email ke employer

[Employer] → Terima pesan undangan (berisi link + token)
           → Kunjungi link → Masukkan OTP dari WA/Email
           → Verifikasi token → Akses Dashboard Employer
           → Isi kuesioner yang ditugaskan
           → Submit → Token dinonaktifkan (single-use)
```

### 6.3 Alur Tracer Study

```
[Super Admin] → Buat sesi Tracer Study
              → Pilih kuesioner yang terlibat
              → Tentukan target (alumni/fakultas/prodi)
              → Aktifkan sesi

[Sistem - via Queue] → Kirim undangan ke semua alumni target (WA + Email)
                     → Kirim token ke semua employer terkait

[Alumni] → Terima undangan → Login → Isi kuesioner → Submit snapshot

[Employer] → Terima token → Verifikasi OTP → Isi kuesioner → Submit snapshot

[Super Admin] → Pantau tingkat respons → Lihat laporan → Ekspor
```

---

## 7. MODUL BREAKDOWN

| No | Modul | Sub-Modul |
|----|-------|-----------|
| 1 | Autentikasi & Otorisasi | Login, OTP, Token Employer, Logout, Session Guard |
| 2 | Manajemen Pengguna | CRUD User, Aktivasi, Reset Password |
| 3 | Manajemen Fakultas | CRUD Fakultas |
| 4 | Manajemen Program Studi | CRUD Program Studi |
| 5 | Kategori Profesi | CRUD Kategori Profesi |
| 6 | Manajemen Profesi | CRUD Profesi |
| 7 | Manajemen Institusi | CRUD Institusi |
| 8 | Detail Institusi | CRUD Detail Institusi |
| 9 | Manajemen Alumni | CRUD Alumni, Import Excel, Export |
| 10 | Permohonan Alumni | CRUD Request, Approve/Reject |
| 11 | Kategori Kuesioner | CRUD Kategori |
| 12 | Manajemen Kuesioner | CRUD Kuesioner & Pertanyaan |
| 13 | Tipe Jawaban | CRUD Tipe Jawaban |
| 14 | Tracer Study | CRUD Sesi, Distribusi, Monitoring |
| 15 | Tracking Pekerjaan Alumni | Employment History, Status |
| 16 | Tracking Employer | Token Undangan, Tracking Respon |
| 17 | Pelaporan & Analitik | Dashboard, Laporan, Ekspor |
| 18 | Sistem Notifikasi | WA, Email, Queue Management |
| 19 | Verifikasi OTP | Generate, Verify, Throttle |
| 20 | Integrasi WA Gateway | Service Layer, Retry |
| 21 | Integrasi SMTP | Mailer, Template Email |
| 22 | Pengaturan Sistem | App Settings, Konfigurasi |
| 23 | Audit Trail | Log Perubahan Data |
| 24 | Activity Log | Log Aktivitas Pengguna |

---

## 8. ROADMAP PENGEMBANGAN

### Phase 1 — Fondasi Sistem (3 Sesi)
- Setup Laravel 12 + Vue 3 + Tailwind + Vite
- Konfigurasi database, migrations, seeders awal
- Modul Auth (Login, OTP, Token Employer)
- RBAC (Policy, Gate, Middleware)
- Layout utama (Sidebar, Header, Routing)

### Phase 2 — Data Master (3 Sesi)
- Manajemen Pengguna
- Manajemen Fakultas & Program Studi
- Kategori Profesi & Profesi
- Manajemen Institusi & Detail Institusi

### Phase 3 — Manajemen Alumni (3 Sesi)
- CRUD Alumni lengkap
- Import/Export Alumni Excel
- Permohonan Alumni (Request Management)
- Employment Tracking

### Phase 4 — Mesin Kuesioner (3 Sesi)
- Kategori Kuesioner
- Kuesioner & Pertanyaan
- Tipe Jawaban
- Pengisian Kuesioner (Alumni & Employer)
- Snapshot Jawaban

### Phase 5 — Tracer Study & Employer (3 Sesi)
- Sesi Tracer Study
- Distribusi ke Alumni
- Token Employer & Akses
- OTP Verification System
- Employer Tracking

### Phase 6 — Notifikasi & Integrasi (3 Sesi)
- WA Gateway UNISYA Integration
- SMTP Integration
- Queue Notifications
- Laravel Scheduler (Reminder)

### Phase 7 — Pelaporan & Analitik (3 Sesi)
- Dashboard Analytics (ApexCharts)
- Semua Laporan
- Ekspor Excel (A4, F4)
- Ekspor PDF (A4, F4)

### Phase 8 — Pengaturan & Keamanan (3 Sesi)
- Settings Management
- Audit Trail
- Activity Log
- Security Hardening

### Phase 9 — Testing & Deployment (3 Sesi)
- Unit & Feature Tests
- Security Testing
- Deployment Ubuntu + aaPanel
- Dokumentasi Deployment

---

*Dokumen ini merupakan referensi utama proyek. Setiap perubahan harus dicatat di 09_CHANGELOG.md.*