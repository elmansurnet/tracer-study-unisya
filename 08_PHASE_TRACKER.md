# 08 — PHASE TRACKER

> **Single source of truth** untuk status pengerjaan project Tracer Study UNISYA.
> Phase dan Task yang sudah ada tidak bisa dihapus demi konsistensi dan audit project. Anda hanya bisa mengupdate progress di setiap PHASE, menambahkan check pada task yang sudah ada, menambahkan task baru jika diperlukan, dan memberikan catatan terkait pengerjaan di setiap PHASE. Progress diupdate setiap akhir session.

---

## 📦 PHASE OVERVIEW

| Phase | Nama | Total Sesi | Selesai | Status |
|-------|------|-----------|---------|--------|
| Phase 1 | Fondasi Sistem | 3 | 3| ✅ SELESAI |
| Phase 2 | Data Master | 3 | 3 | ✅ SELESAI |
| Phase 3 | Manajemen Alumni | 3 | 1 | 🔄 Dalam Pengerjaan |
| Phase 4 | Mesin Kuesioner | 3 | 0 | ⬜ Belum Dimulai |
| Phase 5 | Tracer Study & Employer | 3 | 0 | ⬜ Belum Dimulai |
| Phase 6 | Notifikasi & Integrasi | 3 | 0 | ⬜ Belum Dimulai |
| Phase 7 | Pelaporan & Analitik | 3 | 0 | ⬜ Belum Dimulai |
| Phase 8 | Pengaturan & Keamanan | 3 | 0 | ⬜ Belum Dimulai |
| Phase 9 | Testing & Deployment | 3 | 0 | ⬜ Belum Dimulai |

---

## ✅ PHASE 1 — Foundation & Auth
**Status:** SELESAI  
**Deliverable:** Laravel project setup, Auth (Sanctum), OTP, Middleware, Role Gate, EmployerAccess, Policies (User), Resources (User), DB Migrations (users, otp_codes, employer_access_tokens), RoleSeeder, AppServiceProvider (rate limiting)

---

## ✅ PHASE 2A — Master Data: Fakultas & Program Studi
**Status:** SELESAI  

### Session 2A-A — Migration + Model
- [x] Migration: `create_faculties_table`
- [x] Migration: `create_study_programs_table`
- [x] Model: `Faculty` (HasUlids, SoftDeletes, audit fields, scopes)
- [x] Model: `StudyProgram` (HasUlids, SoftDeletes, BelongsTo Faculty)

### Session 2A-B — Repository + Service
- [x] `FacultyRepository`
- [x] `FacultyService`
- [x] `StudyProgramRepository`
- [x] `StudyProgramService`
- [x] `UserRepository` (Eloquent + Interface)
- [x] `UserService`
- [x] `AuditService` (centralized audit logging)

### Session 2A-C — Controller + Request + Resource + Policy
- [x] `FacultyController`
- [x] `StoreFacultyRequest`, `UpdateFacultyRequest`
- [x] `FacultyResource`
- [x] `FacultyPolicy`
- [x] `StudyProgramController`
- [x] `StoreStudyProgramRequest`, `UpdateStudyProgramRequest`
- [x] `StudyProgramResource`
- [x] `StudyProgramPolicy`
- [x] `UserController`
- [x] `StoreUserRequest`, `UpdateUserRequest`
- [x] `UserResource`
- [x] `UserPolicy`
- [x] Routes (`/faculties`, `/study-programs`, `/users`) ditambahkan ke `api.php`

### Seeder (carry-over — SELESAI)
- [x] `FacultySeeder`
- [x] `StudyProgramSeeder`
- [x] Terdaftar di `DatabaseSeeder`

---

## ✅ PHASE 2B — Master Data: Profesi & Institusi
**Status:** SELESAI  

### Session 2B-A — Migration + Model
- [x] Migration: `create_profession_categories_table`
- [x] Migration: `create_professions_table`
- [x] Migration: `create_institutions_table`
- [x] Migration: `create_institution_details_table`
- [x] Model: `ProfessionCategory`
- [x] Model: `Profession`
- [x] Model: `Institution`
- [x] Model: `InstitutionDetail`

### Session 2B-B — Repository + Service + Controller + Request + Resource + Policy
- [x] `ProfessionCategoryRepository`, `ProfessionCategoryService`
- [x] `ProfessionCategoryController`, `StoreProfessionCategoryRequest`, `UpdateProfessionCategoryRequest`
- [x] `ProfessionCategoryResource`, `ProfessionCategoryPolicy`
- [x] `ProfessionRepository`, `ProfessionService`
- [x] `ProfessionController`, `StoreProfessionRequest`, `UpdateProfessionRequest`
- [x] `ProfessionResource`, `ProfessionPolicy`
- [x] `InstitutionRepository`, `InstitutionService`
- [x] `InstitutionController`, `StoreInstitutionRequest`, `UpdateInstitutionRequest`
- [x] `InstitutionResource`, `InstitutionPolicy`
- [x] Routes 2B ditambahkan ke `api.php`

### Seeder (carry-over — SELESAI)
- [x] `ProfessionCategorySeeder`
- [x] `ProfessionSeeder`
- [x] `InstitutionSeeder`
- [x] Terdaftar di `DatabaseSeeder`

### InstitutionDetail Full Stack (carry-over — SELESAI di 2C)
- [x] `InstitutionDetailRepository`
- [x] `InstitutionDetailService`
- [x] `InstitutionDetailController`
- [x] `StoreInstitutionDetailRequest`, `UpdateInstitutionDetailRequest`
- [x] `InstitutionDetailResource`
- [x] `InstitutionDetailPolicy`
- [x] `InstitutionDetailTab.vue` (Frontend)
- [x] `useInstitutionDetailStore.js`

---

## ✅ PHASE 2C — Audit, Notifikasi & Pengaturan
**Status:** SELESAI — 2026-06-06  

### Session 2C-A — Backend Core
- [x] `AuditTrailObserver` — auto-record CREATE/UPDATE/DELETE/RESTORE ke `audit_trails`
- [x] `AuditTrailController` — index (paginated + filter), show
- [x] `ActivityLogController` — index, show, purge
- [x] `SettingController` — index, show, update, batchUpdate
- [x] `AppSettingSeeder` — 16 setting default (university, tracer, wa_gateway, notification)
- [x] `AuditTrailPolicy`, `AppSettingPolicy`
- [x] `AppServiceProvider` — daftarkan observer untuk 7 model utama
- [x] `AuthServiceProvider` — daftarkan semua policy 2B & 2C
- [x] `DatabaseSeeder` — tambah `AppSettingSeeder`
- [x] `routes/api.php` — tambah routes audit-trail, activity-log, settings, institution detail

### Session 2C-B — Frontend Stores
- [x] `useAuditTrailStore.js`
- [x] `useActivityLogStore.js`
- [x] `useSettingStore.js`
- [x] `useInstitutionDetailStore.js`

### Session 2C-C — Frontend Pages
- [x] `AuditTrailPage.vue` — tabel + filter + detail modal
- [x] `ActivityLogPage.vue` — tabel + filter + detail modal + purge dialog
- [x] `SettingsPage.vue` — tab per group + toggle boolean + batch save
- [x] `InstitutionDetailTab.vue` — form upsert detail institusi

---

## PHASE 3 — MANAJEMEN ALUMNI

**Tujuan:** CRUD Alumni lengkap, import/export, permohonan update, employment tracking.
**Prasyarat:** Phase 2 selesai. Audit Phase 2 sebelum mulai.

---

### ✅ SESSION 3A — CRUD Alumni Dasar

**Status:** ✅ SELESAI — 2026-06-06  
**Commit Batch 1:** Migration + Model + Repository  
**Commit Batch 2:** Service + AlumniEmploymentHistoryRepository + Service  
**Commit Batch 3:** Requests (Admin + AlumniSelf) + Resources + Policies  
**Commit Batch 4:** Controllers (Admin + AlumniSelf) + AuthServiceProvider + routes/api.php  

#### Backend Tasks

- [x] Model `Alumni` — HasUlids, SoftDeletes, audit fields, relasi `studyProgram`, `user`, `employmentHistories`, `tracerStudies`
- [x] Model `AlumniEmploymentHistory` — HasUlids, SoftDeletes, relasi `alumni`, `institution`, `profession`
- [x] Migration `create_alumni_table` — sudah ada dari Phase 1 skeleton, diverifikasi lengkap
- [x] Migration `create_alumni_employment_histories_table` — sudah ada dari Phase 1 skeleton, diverifikasi lengkap
- [x] `AlumniRepository` — paginate (multi-filter), findById, findByNim, byStudyProgram, byGraduationYear, countByEmploymentStatus, graduationYears, create, update, softDelete, restore
- [x] `AlumniEmploymentHistoryRepository` — paginate, findById, byAlumni, currentForAlumni, clearCurrentForAlumni, create, update, softDelete, restore
- [x] `AlumniService` — create, update, updateEmploymentStatus (atomik), delete (guard tracer study), restore, paginate, findOrFail, byStudyProgram, byGraduationYear, countByEmploymentStatus, graduationYears
- [x] `AlumniEmploymentHistoryService` — create (one-current rule), update (is_current conflict), delete (sync is_employed), restore (sync is_employed), syncAlumniEmploymentStatus (private)
- [x] Form Request Admin: `StoreAlumniRequest`, `UpdateAlumniRequest`
- [x] Form Request Admin: `StoreAlumniEmploymentHistoryRequest`, `UpdateAlumniEmploymentHistoryRequest`
- [x] Form Request AlumniSelf: `UpdateProfileRequest` (hanya field kontak), `UpdateEmploymentRequest`
- [x] Form Request AlumniSelf: `StoreEmploymentHistoryRequest`, `UpdateEmploymentHistoryRequest`
- [x] Policy: `AlumniPolicy` — viewAny/view/create/update/updateSelf/delete/restore
- [x] Policy: `AlumniEmploymentHistoryPolicy` — viewAny/view/create/update/delete/restore (alumni self vs admin)
- [x] Resource: `AlumniResource` — semua field + whenLoaded relations (anti-N+1)
- [x] Resource: `AlumniEmploymentHistoryResource` — semua field + whenLoaded relations
- [x] `AlumniController` (Admin) — index, graduationYears, employmentStats, byStudyProgram, store, show, update, destroy, restore
- [x] `AlumniEmploymentHistoryController` (Admin) — index, store, show, update, destroy, restore (nested di bawah alumni/{alumniId})
- [x] `ProfileController` (AlumniSelf) — show, update, updateEmploymentStatus
- [x] `EmploymentHistoryController` (AlumniSelf) — index, store, show, update, destroy, restore
- [x] `AuthServiceProvider` — tambah mapping `Alumni::class => AlumniPolicy::class` dan `AlumniEmploymentHistory::class => AlumniEmploymentHistoryPolicy::class`
- [x] `routes/api.php` — 15 route baru admin alumni + 9 route alumni self-service

#### Frontend Tasks _(direncanakan di session berikutnya)_

- [ ] Halaman `/admin/alumni` — tabel dengan filter (Fakultas, Prodi, Tahun Lulus, Status)
- [ ] Halaman `/admin/alumni/:id` — detail alumni + tab pekerjaan
- [ ] Form step-by-step: Buat & Edit Alumni (3 langkah)
- [ ] Komponen `AlumniCard.vue`
- [ ] Halaman `/alumni/profil` — profil diri alumni
- [ ] Halaman `/alumni/pekerjaan` — riwayat pekerjaan
- [ ] Pinia store: `useAlumniStore`

**Checkpoint tambahan (dari implementasi aktual):**
- [x] AlumniSelf namespace controller baru: `App\Http\Controllers\Api\AlumniSelf\`
- [x] Double-guard ownership pada semua AlumniSelf controllers (`abort_unless alumni_id === user->alumni->id`)
- [x] Business rule `one-current-job` di AlumniEmploymentHistoryService::create() + update()
- [x] Auto-sync `alumni.is_employed` saat riwayat pekerjaan dibuat/diupdate/dihapus/restore
- [x] Admin route nested: `GET /admin/study-programs/{studyProgramId}/alumni` untuk dropdown
- [x] `AlumniService::delete()` — guard: cegah hapus jika masih ada data tracer study terkait

**Catatan Sesi 3A:**
> Session 3A diselesaikan dalam 4 batch pada 2026-06-06.
> Batch 1: Migration sudah ada dari Phase 1 skeleton — diverifikasi; Model Alumni + AlumniEmploymentHistory + Repository dibuat.
> Batch 2: AlumniService + AlumniEmploymentHistoryService dengan business rules lengkap (one-current-job, auto-sync is_employed).
> Batch 3: 8 Request classes (Admin + AlumniSelf) + 2 Resources + 2 Policies.
> Batch 4: 4 Controllers (Admin & AlumniSelf namespace baru) + AuthServiceProvider update + routes/api.php update.
> Frontend tasks dipindahkan ke session berikutnya (3B atau session terpisah).
> Konflik C-06 terdeteksi dan diselesaikan: AlumniSelf controller menggunakan namespace baru `Api\AlumniSelf\` agar tidak bentrok dengan namespace Admin.

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

> ⚠️ **Catatan:** Employment Tracking backend (Repository, Service, Controller, Request, Policy, Resource) **sudah diselesaikan di Session 3A** sebagai bagian dari AlumniEmploymentHistory full-stack. Session 3C akan fokus pada Frontend tasks dan integrasi autocomplete.

#### Backend Tasks _(sudah selesai di 3A)_

- [x] `AlumniEmploymentHistoryRepository` + `AlumniEmploymentHistoryService`
- [x] `AlumniEmploymentHistoryController` (Admin & AlumniSelf)
- [x] Form Request: `StoreAlumniEmploymentHistoryRequest`, `UpdateAlumniEmploymentHistoryRequest`, `StoreEmploymentHistoryRequest`, `UpdateEmploymentHistoryRequest`
- [x] Policy: `AlumniEmploymentHistoryPolicy`
- [x] Resource: `AlumniEmploymentHistoryResource`
- [x] Logika update `alumni.is_employed` dan `alumni.waiting_period_months` otomatis

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
| 2026-06-06 | 3A | AlumniSelf controller gunakan namespace `Api\AlumniSelf\` terpisah | Menghindari naming conflict dengan Admin namespace; akses kontrol lebih jelas |
| 2026-06-06 | 3A | Employment tracking backend digabung di 3A (bukan 3C) | Menghindari dependency gap — AlumniService butuh HistoryService sejak awal |
| 2026-06-06 | 3A | Double-guard ownership di AlumniSelf controllers | `abort_unless` cek kepemilikan SEBELUM Policy untuk fail-fast dan mencegah info disclosure |

---

## 📝 CATATAN KONFLIK YANG DISELESAIKAN

| Kode  | Konflik                            | Resolusi                               |
|-------|------------------------------------|----------------------------------------|
| C-01  | Phase Tracker 2B vs permintaan "Angkatan & Alumni" | Ikuti Tracker; Alumni masuk Phase 3 |
| C-02  | InstitutionDetail carry-over 2B    | Diselesaikan di Phase 2C              |
| C-03  | Seeder Fakultas/StudyProgram       | Sudah ada di repo sejak 2A            |
| C-04  | SettingService sudah ada           | Digunakan langsung di SettingController|
| C-05  | AppSetting/AuditTrail/InstitutionDetail Model sudah ada | Skip create, langsung buat Stack |
| C-06  | AlumniSelf controller namespace conflict | Gunakan namespace `Api\AlumniSelf\` terpisah dari `Api\Admin\` |

---

## KONFLIK & MASALAH YANG TERDETEKSI

*Catat semua konflik atau masalah yang perlu diselesaikan.*

| ID | Tanggal | Deskripsi | Status | Solusi |
|----|---------|-----------|--------|--------|
| — | — | — | — | — |

---

*File ini adalah sumber kebenaran tunggal untuk progress development. Update setiap akhir sesi.*
