# Phase Tracker — Tracer Study UNISYA

> **Updated:** 2026-06-06  
> **Last Session:** Phase 2A-2C (AUDIT)   
> **Overall Progress:** Phase 2A-2C (AUDIT)

---

## ✅ PHASE 1 — Setup & Infrastruktur (COMPLETE)

### Session A — Laravel Foundation
- [x] Install Laravel 12, PHP 8.3
- [x] Configure `.env`, database MySQL 8
- [x] Install packages: spatie/laravel-permission, spatie/laravel-activitylog, laravel/sanctum
- [x] Base Service, Repository, Resource classes
- [x] Exception Handler JSON response

### Session B — Vue 3 Foundation
- [x] Install Vue 3, Vite, Pinia, Vue Router
- [x] AdminLayout, AlumniLayout, EmployerLayout
- [x] Base components: AppButton, AppInput, AppModal, AppTable, AppPagination, AppBadge, AppConfirm
- [x] HTTP client (axios), auth store, ui store
- [x] Login page, OTP page, Dashboard pages

### Session C — Auth & Security
- [x] Sanctum SPA authentication
- [x] OTP login flow
- [x] Role-based middleware
- [x] CSRF protection

---

### SESSION 2A — Manajemen Pengguna & Akademik

**Status:** ⬜ Belum Dimulai
**Target:** CRUD User, Fakultas, Program Studi berfungsi penuh

#### Backend Tasks

- [ ] `UserRepository` + `UserService`
- [ ] `UserController` (Admin) — CRUD + reset password + toggle aktif
- [ ] Form Request: `StoreUserRequest`, `UpdateUserRequest`, `ResetPasswordRequest`
- [ ] Policy: `UserPolicy` (update, delete, view)
- [ ] Resource: `UserResource`
- [ ] `FacultyRepository` + `FacultyService`
- [ ] `FacultyController` (Admin) — CRUD
- [ ] Form Request: `StoreFacultyRequest`, `UpdateFacultyRequest`
- [ ] Policy: `FacultyPolicy`
- [ ] Resource: `FacultyResource`
- [ ] `StudyProgramRepository` + `StudyProgramService`
- [ ] `StudyProgramController` (Admin) — CRUD
- [ ] Form Request: `StoreStudyProgramRequest`, `UpdateStudyProgramRequest`
- [ ] Policy: `StudyProgramPolicy`
- [ ] Resource: `StudyProgramResource`
- [ ] Seeder: `FacultySeeder` (data UNISYA)
- [ ] Seeder: `StudyProgramSeeder` (data UNISYA)
- [ ] Factory: `FacultyFactory`, `StudyProgramFactory`

#### Frontend Tasks

- [ ] Halaman `/admin/pengguna` — tabel user (sortable, searchable, paginated)
- [ ] Form modal/drawer: Buat & Edit User
- [ ] Komponen `AppTable.vue` (sort, pagination, empty state, skeleton)
- [ ] Komponen `AppPagination.vue`
- [ ] Halaman `/admin/fakultas` — tabel + CRUD modal
- [ ] Halaman `/admin/program-studi` — tabel + CRUD modal (filter by fakultas)
- [ ] Pinia store: `useUserStore`, `useFacultyStore`, `useStudyProgramStore`

**Catatan Sesi 2A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 2B — Manajemen Profesi & Institusi

**Status:** ⬜ Belum Dimulai
**Target:** CRUD Profesi dan Institusi berfungsi penuh

#### Backend Tasks

- [ ] `ProfessionCategoryRepository` + `ProfessionCategoryService`
- [ ] `ProfessionCategoryController` — CRUD
- [ ] Form Request: `StoreProfessionCategoryRequest`, `UpdateProfessionCategoryRequest`
- [ ] Resource: `ProfessionCategoryResource`
- [ ] `ProfessionRepository` + `ProfessionService`
- [ ] `ProfessionController` — CRUD
- [ ] Form Request: `StoreProfessionRequest`, `UpdateProfessionRequest`
- [ ] Resource: `ProfessionResource`
- [ ] `InstitutionRepository` + `InstitutionService`
- [ ] `InstitutionController` — CRUD + detail
- [ ] `InstitutionDetailController` — CRUD
- [ ] Form Request: `StoreInstitutionRequest`, `UpdateInstitutionRequest`
- [ ] Form Request: `StoreInstitutionDetailRequest`, `UpdateInstitutionDetailRequest`
- [ ] Resource: `InstitutionResource`, `InstitutionDetailResource`
- [ ] Seeder: `ProfessionCategorySeeder`, `ProfessionSeeder`

#### Frontend Tasks

- [ ] Halaman `/admin/kategori-profesi` — tabel + CRUD
- [ ] Halaman `/admin/profesi` — tabel + CRUD (filter by kategori)
- [ ] Halaman `/admin/institusi` — tabel + CRUD
- [ ] Tab "Detail Institusi" dalam halaman detail institusi
- [ ] Pinia store: `useProfessionStore`, `useInstitutionStore`

**Catatan Sesi 2B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 2C — Audit, Notifikasi & Pengaturan Awal

**Status:** ⬜ Belum Dimulai
**Target:** Model Observer untuk audit trail, Settings Management dasar

#### Backend Tasks

- [ ] `AuditTrailObserver` — register untuk semua model yang diaudit
- [ ] `AuditTrailController` — read-only list + filter
- [ ] `ActivityLogController` — read-only list + filter
- [ ] `SettingService` — get/set setting dengan cache
- [ ] `SettingController` — get all, get by group, bulk update
- [ ] `AppSetting` Model + Seeder (settings default)
- [ ] Model Observer terdaftar di `AppServiceProvider`

#### Frontend Tasks

- [ ] Halaman `/admin/audit-trail` — tabel audit (filter model, event, user, tanggal)
- [ ] Halaman `/admin/activity-log` — tabel aktivitas (filter user, tanggal)
- [ ] Halaman `/admin/pengaturan` — form settings (tab: Umum, WA Gateway, SMTP)
- [ ] Pinia store: `useSettingStore`

**Catatan Sesi 2C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 3 — MANAJEMEN ALUMNI

**Tujuan:** CRUD Alumni lengkap, import/export, permohonan update, employment tracking.
**Prasyarat:** Phase 2 selesai. Audit Phase 2 sebelum mulai.

---

### SESSION 3A — CRUD Alumni Dasar

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Model `Alumni` + `AlumniEmploymentHistory` + `AlumniRequest`
- [ ] `AlumniRepository` + `AlumniService`
- [ ] `AlumniController` (Admin) — CRUD + filter + search
- [ ] Form Request: `StoreAlumniRequest`, `UpdateAlumniRequest`
- [ ] Policy: `AlumniPolicy`
- [ ] Resource: `AlumniResource`, `AlumniDetailResource`
- [ ] `AlumniController` (Alumni Self) — profile, employment
- [ ] Form Request: `UpdateAlumniProfileRequest`
- [ ] File upload foto alumni (simpan di `storage/app/private/alumni/photos`)
- [ ] Seeder: `AlumniSeeder` (data dummy)
- [ ] Factory: `AlumniFactory`

#### Frontend Tasks

- [ ] Halaman `/admin/alumni` — tabel dengan filter (Fakultas, Prodi, Tahun Lulus, Status)
- [ ] Halaman `/admin/alumni/:id` — detail alumni + tab pekerjaan
- [ ] Form step-by-step: Buat & Edit Alumni (3 langkah)
- [ ] Komponen `AlumniCard.vue`
- [ ] Halaman `/alumni/profil` — profil diri alumni
- [ ] Halaman `/alumni/pekerjaan` — riwayat pekerjaan
- [ ] Pinia store: `useAlumniStore`

**Catatan Sesi 3A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 3B — Import/Export & Permohonan Alumni

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] `AlumniImport` class (Laravel Excel) — dengan validasi baris per baris
- [ ] `AlumniExport` class (Laravel Excel) — dengan filter
- [ ] `AlumniPdfExport` class (DomPDF) — template A4 dan F4
- [ ] Endpoint import: `POST /api/v1/admin/alumni/import`
- [ ] Endpoint export: `GET /api/v1/admin/alumni/export/excel`
- [ ] Endpoint export: `GET /api/v1/admin/alumni/export/pdf`
- [ ] Template Excel import (file contoh untuk download)
- [ ] `AlumniRequestRepository` + `AlumniRequestService`
- [ ] `AlumniRequestController` (Admin) — list, detail, approve, reject
- [ ] `AlumniRequestController` (Alumni) — create, list milik sendiri
- [ ] Form Request: `StoreAlumniRequestRequest`
- [ ] Policy: `AlumniRequestPolicy`
- [ ] Resource: `AlumniRequestResource`

#### Frontend Tasks

- [ ] Tombol Import Excel + modal upload + preview error validasi
- [ ] Komponen `ExportButton.vue` (dropdown: Excel/PDF, ukuran A4/F4)
- [ ] Halaman `/alumni/permohonan` — list permohonan + form buat permohonan
- [ ] Halaman `/admin/permohonan-alumni` — tabel permohonan + approve/reject

**Catatan Sesi 3B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 3C — Employment Tracking

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] `EmploymentRepository` + `EmploymentService`
- [ ] `EmploymentController` (Admin & Alumni) — CRUD riwayat pekerjaan
- [ ] Form Request: `StoreEmploymentRequest`, `UpdateEmploymentRequest`
- [ ] Policy: `EmploymentPolicy`
- [ ] Resource: `EmploymentResource`
- [ ] Logika update `alumni.is_employed` dan `alumni.waiting_period_months` otomatis

#### Frontend Tasks

- [ ] Komponen riwayat pekerjaan (list + form tambah/edit)
- [ ] Integrasi autocomplete institusi saat tambah pekerjaan
- [ ] Integrasi autocomplete profesi saat tambah pekerjaan

**Catatan Sesi 3C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 4 — MESIN KUESIONER

**Tujuan:** Builder kuesioner, tipe jawaban, pengisian oleh alumni & employer.
**Prasyarat:** Phase 3 selesai. Audit Phase 3 sebelum mulai.

---

### SESSION 4A — Builder Kuesioner

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Model `QuestionnaireCategory`, `AnswerType`, `Questionnaire`, `QuestionnaireQuestion`
- [ ] CRUD `QuestionnaireCategory` + Repository + Service + Controller + Request + Policy
- [ ] CRUD `AnswerType` + Repository + Service + Controller + Request + Policy
- [ ] CRUD `Questionnaire` + Repository + Service + Controller + Request + Policy
- [ ] CRUD `QuestionnaireQuestion` (termasuk reorder) + Repository + Service + Controller
- [ ] Resource: semua model kuesioner
- [ ] Seeder: `AnswerTypeSeeder` (true_false, scale_1_5, scale_1_10)
- [ ] Seeder: `QuestionnaireCategorySeeder` (data awal)
- [ ] Factory: `QuestionnaireFactory`, `QuestionnaireQuestionFactory`

#### Frontend Tasks

- [ ] Halaman `/admin/kategori-kuesioner` — CRUD
- [ ] Halaman `/admin/tipe-jawaban` — CRUD
- [ ] Halaman `/admin/kuesioner` — tabel kuesioner
- [ ] Halaman `/admin/kuesioner/:id/pertanyaan` — builder pertanyaan
- [ ] Komponen `QuestionnaireBuilder.vue` (drag-and-drop urutan pertanyaan)
- [ ] Komponen `QuestionCard.vue` (tampilan tiap pertanyaan)
- [ ] Preview kuesioner real-time (panel samping)

**Catatan Sesi 4A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 4B — Pengisian Kuesioner Alumni

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Model `QuestionnaireResponse`, `QuestionnaireAnswer`
- [ ] `QuestionnaireResponseService` — logika pengisian + snapshot
- [ ] `QuestionnaireResponseController` (Alumni) — list, detail, submit
- [ ] Form Request: `SubmitQuestionnaireRequest`
- [ ] Validasi: cek sudah pernah mengisi (duplicate prevention)
- [ ] Logic: simpan `questionnaire_snapshot` dan `question_snapshot` (immutable)
- [ ] Factory: `QuestionnaireResponseFactory`

#### Frontend Tasks

- [ ] Halaman `/alumni/tracer-study` — daftar tracer study aktif
- [ ] Halaman `/alumni/tracer-study/:id/isi` — wizard pengisian kuesioner
- [ ] Komponen `AnswerScaleInput.vue` (slider 1-5 dan 1-10)
- [ ] Komponen `AnswerTrueFalseInput.vue` (toggle)
- [ ] Progress bar pengisian
- [ ] Auto-save draft jawaban (sessionStorage)
- [ ] Konfirmasi sebelum submit final

**Catatan Sesi 4B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 4C — Pengisian Kuesioner Employer

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] `QuestionnaireResponseController` (Employer) — list kuesioner + submit
- [ ] Middleware `CheckEmployerToken` — validasi token ability 'employer'
- [ ] Validasi: token employer hanya bisa mengisi 1 kali per kuesioner
- [ ] Endpoint employer: `GET /api/v1/employer/questionnaires`
- [ ] Endpoint employer: `POST /api/v1/employer/questionnaires/:id/submit`

#### Frontend Tasks

- [ ] Halaman `/employer/dashboard` — sambutan + daftar kuesioner
- [ ] Halaman `/employer/kuesioner/:id` — pengisian kuesioner dalam format card
- [ ] Halaman `/employer/selesai` — konfirmasi submit + nomor respons
- [ ] Layout employer yang minimal dan bersih

**Catatan Sesi 4C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 5 — TRACER STUDY & EMPLOYER

**Tujuan:** Sesi Tracer Study, distribusi undangan, token employer, monitoring respons.
**Prasyarat:** Phase 4 selesai. Audit Phase 4 sebelum mulai.

---

### SESSION 5A — Manajemen Sesi Tracer Study

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Model `TracerStudy`, `TracerStudyQuestionnaire`
- [ ] `TracerStudyRepository` + `TracerStudyService`
- [ ] `TracerStudyController` (Admin) — CRUD + activate + complete
- [ ] Form Request: `StoreTracerStudyRequest`, `UpdateTracerStudyRequest`
- [ ] Policy: `TracerStudyPolicy`
- [ ] Resource: `TracerStudyResource`, `TracerStudyDetailResource`
- [ ] Endpoint aktivasi: kirim undangan via Queue saat diaktifkan
- [ ] Endpoint stats: `GET /api/v1/admin/tracer-studies/:id/stats`
- [ ] Endpoint responses: `GET /api/v1/admin/tracer-studies/:id/responses`
- [ ] Seeder: `TracerStudySeeder` (data dummy)

#### Frontend Tasks

- [ ] Halaman `/admin/tracer-study` — tabel sesi
- [ ] Halaman `/admin/tracer-study/:id` — detail + monitoring + respons
- [ ] Form buat & edit sesi Tracer Study
- [ ] Komponen `TracerStudyStatusBadge.vue`
- [ ] Gauge/progress chart tingkat respons (ApexCharts)
- [ ] Pinia store: `useTracerStudyStore`

**Catatan Sesi 5A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 5B — Token Employer & OTP Flow

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] `EmployerAccessService` — create, validate, revoke token
- [ ] Artisan command: `CleanupExpiredTokens`
- [ ] Endpoint alumni: buat token undangan employer
- [ ] Endpoint alumni: cabut token
- [ ] Endpoint employer: request OTP
- [ ] Endpoint employer: verify OTP
- [ ] Logic: token_plain di-null setelah pengiriman
- [ ] Policy: `EmployerAccessTokenPolicy`

#### Frontend Tasks

- [ ] Halaman `/alumni/employer` — daftar token + form undang employer
- [ ] Komponen: form undang employer (pilih institusi, input kontak)
- [ ] Halaman `/employer/akses` — landing page + input token
- [ ] Halaman `/employer/verifikasi-otp` — OTP verification
- [ ] Komponen `OtpInput.vue` — 6 digit auto-focus

**Catatan Sesi 5B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 5C — Tracking & Monitoring

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Dashboard Admin: endpoint `GET /api/v1/admin/reports/dashboard`
- [ ] Logic: hitung tingkat respons per sesi Tracer Study
- [ ] Logic: hitung statistik employment (per fakultas, per prodi)

#### Frontend Tasks

- [ ] Halaman `/admin/dashboard` — KPI cards + chart ApexCharts
- [ ] Komponen `KpiCard.vue` (ikon, nilai, tren)
- [ ] Komponen `ChartWidget.vue` (wrapper ApexCharts)
- [ ] Chart: Donut status pekerjaan alumni
- [ ] Chart: Bar alumni per fakultas
- [ ] Chart: Line tren wisuda per tahun
- [ ] Chart: Gauge tingkat respons tracer study aktif

**Catatan Sesi 5C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 6 — NOTIFIKASI & INTEGRASI

**Tujuan:** WA Gateway, SMTP, Queue notifications, Laravel Scheduler (reminder).
**Prasyarat:** Phase 5 selesai. Audit Phase 5 sebelum mulai.

---

### SESSION 6A — WhatsApp Gateway Integration

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] `WhatsAppGatewayService` — send(), sanitizePhoneNumber(), retry logic
- [ ] Job: `SendWhatsAppNotification`
- [ ] Notification: `OtpLoginNotification` (channel: WA)
- [ ] Notification: `EmployerInvitationNotification` (channel: WA)
- [ ] Notification: `TracerStudyInvitationNotification` (channel: WA)
- [ ] Notification: `TracerStudyReminderNotification` (channel: WA)
- [ ] Model `NotificationLog` — catat setiap pengiriman WA
- [ ] Endpoint admin: `POST /api/v1/admin/settings/test-wa` (test pengiriman)
- [ ] Konfigurasi: api_key & sender diambil dari `AppSetting` (bukan hardcoded)

**WA Gateway Endpoint:**
```
URL    : https://wacenter.unisya.ac.id/send-message
Method : POST (JSON) / GET (Query String)
Params : api_key, sender, number, message, footer (opt), msgid (opt), full (opt)
```

**Catatan Sesi 6A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 6B — SMTP & Email Templates

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] `SmtpService` — konfigurasi SMTP dari `AppSetting` secara dinamis
- [ ] Job: `SendEmailNotification`
- [ ] Notification: `OtpLoginNotification` (channel: email)
- [ ] Notification: `EmployerInvitationNotification` (channel: email)
- [ ] Notification: `TracerStudyInvitationNotification` (channel: email)
- [ ] Template Blade email: OTP, Undangan Tracer Study, Undangan Employer
- [ ] Endpoint admin: `POST /api/v1/admin/settings/test-smtp`

**Catatan Sesi 6B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 6C — Queue Worker & Scheduler

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Konfigurasi Queue driver: `database`
- [ ] Setup Supervisor config untuk `queue:work`
- [ ] Scheduler: daftarkan semua command di `Kernel.php`
  - [ ] `CleanupExpiredOtp` — setiap jam
  - [ ] `CleanupExpiredTokens` — setiap jam
  - [ ] `SendTracerStudyReminders` — setiap hari jam 08:00
- [ ] Notification log: catat status success/failed di `notification_logs`
- [ ] Failed job handling: retry & log

#### Frontend Tasks

- [ ] Halaman `/admin/pengaturan` — tab "WA Gateway" (api_key, sender, test)
- [ ] Halaman `/admin/pengaturan` — tab "SMTP" (host, port, user, pass, test)
- [ ] Komponen test koneksi dengan status indikator

**Catatan Sesi 6C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 7 — PELAPORAN & ANALITIK

**Tujuan:** Dashboard analitik lengkap, semua laporan, export Excel & PDF.
**Prasyarat:** Phase 6 selesai. Audit Phase 6 sebelum mulai.

---

### SESSION 7A — Laporan Alumni & Distribusi

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] `ReportService` — semua query laporan dengan filter
- [ ] Endpoint: `GET /api/v1/admin/reports/alumni-distribution`
- [ ] Endpoint: `GET /api/v1/admin/reports/faculty-analytics`
- [ ] Endpoint: `GET /api/v1/admin/reports/program-analytics`
- [ ] Filter: `start_date`, `end_date`, `faculty_id`, `study_program_id`
- [ ] `AlumniDistributionExport` (Excel + PDF, A4/F4)

#### Frontend Tasks

- [ ] Halaman `/admin/laporan` — filter global + tab
- [ ] Tab "Distribusi Alumni" — chart + tabel
- [ ] Tab "Per Fakultas" — chart bar + tabel detail
- [ ] Tab "Per Program Studi" — chart + tabel
- [ ] Komponen `AppDatePicker.vue` (date range picker)
- [ ] Pinia store: `useReportStore`

**Catatan Sesi 7A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 7B — Laporan Pekerjaan & Masa Tunggu

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Endpoint: `GET /api/v1/admin/reports/employment-analytics`
- [ ] Endpoint: `GET /api/v1/admin/reports/waiting-period`
- [ ] Endpoint: `GET /api/v1/admin/reports/employer-analytics`
- [ ] `EmploymentAnalyticsExport` (Excel + PDF)

#### Frontend Tasks

- [ ] Tab "Analitik Pekerjaan" — chart + tabel
- [ ] Tab "Masa Tunggu" — distribusi masa tunggu dalam bulan
- [ ] Tab "Employer" — top employer + statistik

**Catatan Sesi 7B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 7C — Laporan Kuesioner & Export

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Endpoint: `GET /api/v1/admin/reports/questionnaire/:id`
- [ ] Export universal: `GET /api/v1/admin/reports/export/excel`
- [ ] Export universal: `GET /api/v1/admin/reports/export/pdf`
- [ ] DomPDF: template laporan A4 dan F4 (header UNISYA, tabel, chart)
- [ ] `ExportService` — orchestrator semua tipe export

#### Frontend Tasks

- [ ] Tab "Kuesioner" — pilih kuesioner + lihat hasil per pertanyaan
- [ ] Chart hasil per pertanyaan (bar untuk skala, donut untuk T/F)
- [ ] Tombol export dengan dropdown format & ukuran kertas

**Catatan Sesi 7C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 8 — PENGATURAN & KEAMANAN

**Tujuan:** Settings Management lengkap, security hardening, audit trail UI.
**Prasyarat:** Phase 7 selesai. Audit Phase 7 sebelum mulai.

---

### SESSION 8A — Settings Management Lengkap

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Setting groups: `general`, `wa_gateway`, `smtp`, `security`, `notification`
- [ ] Enkripsi nilai setting sensitif (api_key, password SMTP)
- [ ] Endpoint bulk update setting per group
- [ ] Seeder: lengkapi `AppSettingSeeder` dengan semua setting

#### Frontend Tasks

- [ ] Halaman `/admin/pengaturan` — tab: Umum | WA Gateway | SMTP | Keamanan | Notifikasi
- [ ] Form Umum: nama aplikasi, logo, URL
- [ ] Form WA Gateway: api_key (masked), sender, tombol test
- [ ] Form SMTP: host, port, enkripsi, username, password (masked), tombol test
- [ ] Form Keamanan: session timeout, OTP expiry, max OTP attempts

**Catatan Sesi 8A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 8B — Security Hardening

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Verifikasi semua Rate Limiter terpasang
- [ ] Verifikasi semua Policy aktif
- [ ] Verifikasi SecurityHeaders middleware aktif
- [ ] Verifikasi CSP header dikonfigurasi dengan benar
- [ ] Verifikasi tidak ada plain password/OTP/token tersimpan di DB
- [ ] Verifikasi tidak ada data sensitif di log
- [ ] Jalankan `php artisan security:audit` (custom command)
- [ ] Buat Artisan command: `app:security-audit` — cek semua security checklist

#### Frontend Tasks

- [ ] Verifikasi semua form memiliki validasi client-side
- [ ] Verifikasi tidak ada data sensitif tersimpan di localStorage
- [ ] Verifikasi CSRF token dikirim di semua request

**Catatan Sesi 8B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 8C — Notifikasi In-App

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Tabel `notifications` (Laravel default morphable)
- [ ] Endpoint: `GET /api/v1/notifications` — unread list
- [ ] Endpoint: `PUT /api/v1/notifications/:id/read`
- [ ] Endpoint: `PUT /api/v1/notifications/read-all`
- [ ] Endpoint: `GET /api/v1/notifications/unread-count`
- [ ] Kirim notifikasi in-app saat: permohonan alumni disetujui/ditolak, tracer study aktif

#### Frontend Tasks

- [ ] Badge notifikasi di header (unread count)
- [ ] Panel notifikasi slide-in dari kanan
- [ ] Pinia store: `useNotificationStore`
- [ ] Auto-refresh unread count setiap 60 detik

**Catatan Sesi 8C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 9 — TESTING & DEPLOYMENT

**Tujuan:** Unit test, feature test, security test, deployment ke Ubuntu + aaPanel.
**Prasyarat:** Phase 8 selesai. Full audit semua phase sebelum mulai.

---

### SESSION 9A — Unit & Feature Tests

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Test: `AuthTest` — login, OTP, logout, invalid credentials
- [ ] Test: `OtpTest` — generate, verify, expired, max attempts
- [ ] Test: `EmployerTokenTest` — create, use, revoke, expired
- [ ] Test: `AlumniTest` — CRUD, import, export, validation
- [ ] Test: `QuestionnaireTest` — builder, submit, snapshot immutability
- [ ] Test: `TracerStudyTest` — create, activate, complete, stats
- [ ] Test: `ReportTest` — semua endpoint laporan
- [ ] Test: `RateLimitTest` — verifikasi rate limiter bekerja
- [ ] Test: `PolicyTest` — verifikasi setiap role hanya bisa akses yang sesuai
- [ ] Jalankan: `php artisan test --coverage`

#### Frontend Tasks

- [ ] Setup Vitest untuk unit test Vue components
- [ ] Test komponen: `OtpInput`, `QuestionnaireBuilder`, `AppTable`

**Catatan Sesi 9A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 9B — Security Testing

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Test: SQL Injection — semua input form
- [ ] Test: XSS — semua output field
- [ ] Test: CSRF — verifikasi token required
- [ ] Test: Mass Assignment — verifikasi field tidak bisa di-inject
- [ ] Test: Rate Limiting — verifikasi 429 saat limit terlampaui
- [ ] Test: Unauthorized Access — verifikasi 403 saat akses tanpa izin
- [ ] Test: File Upload — upload file berbahaya (PHP, .htaccess)
- [ ] Verifikasi: OTP plain tidak tersimpan di DB
- [ ] Verifikasi: Token plain di-null setelah pengiriman

**Catatan Sesi 9B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 9C — Deployment & Dokumentasi

**Status:** ⬜ Belum Dimulai

#### Deployment Tasks

- [ ] Setup server Ubuntu 22.04 + aaPanel
- [ ] Install PHP 8.3 + ekstensi yang diperlukan
- [ ] Install MySQL 8.0
- [ ] Install Nginx / Apache
- [ ] Setup SSL Let's Encrypt
- [ ] Clone project ke server
- [ ] Konfigurasi `.env` production
- [ ] Jalankan `composer install --no-dev --optimize-autoloader`
- [ ] Jalankan `npm run build` (Vue SPA build)
- [ ] Jalankan `php artisan migrate --force`
- [ ] Jalankan `php artisan db:seed --class=ProductionSeeder`
- [ ] Jalankan `php artisan config:cache && php artisan route:cache`
- [ ] Setup Supervisor untuk `queue:work`
- [ ] Setup crontab untuk `schedule:run`
- [ ] Konfigurasi permissions file & direktori
- [ ] Verifikasi semua fitur berjalan di production

#### Dokumentasi Tasks

- [ ] Buat `DEPLOYMENT.md` — panduan deployment
- [ ] Buat `USER_MANUAL_ADMIN.md` — panduan admin
- [ ] Buat `USER_MANUAL_ALUMNI.md` — panduan alumni
- [ ] Update semua file dokumentasi (01-09) ke versi final

**Catatan Sesi 9C:**
> _Isi catatan setelah sesi selesai_

---

## KEPUTUSAN TEKNIS PENTING

*Catat semua keputusan arsitektural/teknis yang dibuat selama development.*

| Tanggal | Phase/Sesi | Keputusan | Alasan |
|---------|-----------|-----------|--------|
| 2026-06-04 | — | UUID CHAR(36) untuk semua PK | Mencegah enumerasi ID, mendukung distribusi |
| 2026-06-04 | — | Queue driver: database (bukan Redis) | Kemudahan deployment tanpa instalasi Redis |
| 2026-06-04 | — | WA Gateway: POST JSON (bukan GET) | Lebih aman untuk data sensitif (OTP, token) |
| 2026-06-04 | — | OTP disimpan dalam bentuk hash (bcrypt) | Keamanan — plain OTP tidak pernah tersimpan |
| 2026-06-04 | — | Employer token: SHA-256 hash + plain di-null | Token sekali pakai, plain dihapus setelah dikirim |
| 2026-06-04 | — | Snapshot immutable pada questionnaire_responses | Perubahan kuesioner tidak merusak data historis |
| 2026-06-04 | — | SoftDeletes pada semua tabel master | Data tidak pernah benar-benar terhapus permanen |

---

## KONFLIK & MASALAH YANG TERDETEKSI

*Catat semua konflik atau masalah yang perlu diselesaikan.*

| ID | Tanggal | Deskripsi | Status | Solusi |
|----|---------|-----------|--------|--------|
| — | — | — | — | — |

---

*File ini adalah sumber kebenaran tunggal untuk progress development. Update setiap akhir sesi.*
