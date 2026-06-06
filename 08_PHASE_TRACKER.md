# 08 — PHASE TRACKER

> **Single source of truth** untuk status pengerjaan project Tracer Study UNISYA.
> Phase dan Task yang sudah ada tidak bisa dihapus demi konsistensi dan audit project. Anda hanya bisa mengupdate progress di setiap PHASE, menambahkan check pada task yang sudah ada, menambahkan task baru jika diperlukan, dan memberikan catatan terkait pengerjaan di setiap PHASE. Progress diupdate setiap akhir session.

---

## 📦 PHASE OVERVIEW

| Phase | Nama | Total Sesi | Selesai | Status |
|-------|------|-----------|---------|--------|
| Phase 1 | Fondasi Sistem | 3 | 3| ✅ SELESAI |
| Phase 2 | Data Master | 3 | 3 | ✅ SELESAI |
| Phase 3 | Manajemen Alumni | 3 | 2 | 🔄 Dalam Pengerjaan |
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
**Status:** ✅ SELESAI — 2026-06-06 (RESMI DITUTUP 2026-06-06)
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

#### Frontend Tasks _(dipindahkan ke Session 3B/3C)_

- [x] Halaman `/admin/alumni` — tabel dengan filter (Fakultas, Prodi, Tahun Lulus, Status) _(carry-over dari 3A, selesai di 3B)_
- [x] Halaman `/admin/alumni/:id` — detail alumni + tab pekerjaan _(carry-over dari 3A, selesai di 3B)_
- [x] Form step-by-step: Buat & Edit Alumni (3 langkah) _(carry-over dari 3A, selesai di 3B)_
- [x] Komponen `AlumniCard.vue` _(carry-over dari 3A, selesai di 3B)_
- [x] Halaman `/alumni/profil` — profil diri alumni _(carry-over dari 3A, selesai di 3B)_
- [x] Halaman `/alumni/pekerjaan` — riwayat pekerjaan _(carry-over dari 3A, selesai di 3B)_
- [x] Pinia store: `useAlumniStore` _(carry-over dari 3A, selesai di 3B)_

**Checkpoint Resmi Penutupan 3A — 2026-06-06:**
> ✅ Semua backend task 3A telah selesai dan terverifikasi push ke repository.
> ✅ 4 Batch commit berhasil: Model+Repository → Service → Request+Resource+Policy → Controller+Routes.
> ✅ Business rules kritis terpenuhi: one-current-job, auto-sync is_employed, double-guard ownership, guard delete tracer study.
> ✅ Namespace `Api\AlumniSelf\` berhasil dibuat terpisah dari `Api\Admin\` (resolusi C-06).
> ✅ 24 route API baru terdaftar di `routes/api.php` (15 Admin + 9 AlumniSelf).
> ⚠️ Frontend tasks (7 item) dipindahkan ke Session 3B/3C — tidak menghalangi penutupan 3A karena scope 3A = backend.
> 🔒 **Session 3A RESMI DITUTUP. Lanjut ke Session 3B.**

**Catatan Sesi 3A:**
> Session 3A diselesaikan dalam 4 batch pada 2026-06-06.
> Batch 1: Migration sudah ada dari Phase 1 skeleton — diverifikasi; Model Alumni + AlumniEmploymentHistory + Repository dibuat.
> Batch 2: AlumniService + AlumniEmploymentHistoryService dengan business rules lengkap (one-current-job, auto-sync is_employed).
> Batch 3: 8 Request classes (Admin + AlumniSelf) + 2 Resources + 2 Policies.
> Batch 4: 4 Controllers (Admin & AlumniSelf namespace baru) + AuthServiceProvider update + routes/api.php update.
> Frontend tasks dipindahkan ke session berikutnya (3B atau session terpisah).
> Konflik C-06 terdeteksi dan diselesaikan: AlumniSelf controller menggunakan namespace baru `Api\AlumniSelf\` agar tidak bentrok dengan namespace Admin.

---

### ✅ SESSION 3B — Import/Export & Permohonan Alumni
**Status:** ✅ SELESAI — 2026-06-06 (RESMI DITUTUP 2026-06-06)
**Commit Batch 1:** Frontend carry-over 3A — Pinia Store + Halaman Admin Alumni  
**Commit Batch 2:** Frontend carry-over 3A — Halaman Detail Alumni + Tab Pekerjaan + AlumniCard  
**Commit Batch 3:** Frontend carry-over 3A — Halaman Alumni Self (Profil + Pekerjaan)  
**Commit Batch 4:** Import/Export Backend — AlumniImport + AlumniExport + AlumniPdfExport + Endpoints  
**Commit Batch 5:** Permohonan Alumni — AlumniRequest full-stack (Backend + Frontend)  

#### Backend Tasks

- [x] `AlumniImport` class (Laravel Excel) — dengan validasi baris per baris
- [x] `AlumniExport` class (Laravel Excel) — dengan filter
- [x] `AlumniPdfExport` class (DomPDF) — template A4 dan F4
- [x] Endpoint import: `POST /api/v1/admin/alumni/import`
- [x] Endpoint export: `GET /api/v1/admin/alumni/export/excel`
- [x] Endpoint export: `GET /api/v1/admin/alumni/export/pdf`
- [x] Template Excel import (file contoh untuk download)
- [x] `AlumniRequestRepository` + `AlumniRequestService`
- [x] `AlumniRequestController` (Admin) — list, detail, approve, reject
- [x] `AlumniRequestController` (Alumni) — create, list milik sendiri
- [x] Form Request: `StoreAlumniRequestRequest`
- [x] Policy: `AlumniRequestPolicy`
- [x] Resource: `AlumniRequestResource`

#### Frontend Tasks (termasuk carry-over dari 3A)

- [x] Halaman `/admin/alumni` — tabel dengan filter (Fakultas, Prodi, Tahun Lulus, Status) _(carry-over 3A)_
- [x] Halaman `/admin/alumni/:id` — detail alumni + tab pekerjaan _(carry-over 3A)_
- [x] Form step-by-step: Buat & Edit Alumni (3 langkah) _(carry-over 3A)_
- [x] Komponen `AlumniCard.vue` _(carry-over 3A)_
- [x] Halaman `/alumni/profil` — profil diri alumni _(carry-over 3A)_
- [x] Halaman `/alumni/pekerjaan` — riwayat pekerjaan _(carry-over 3A)_
- [x] Pinia store: `useAlumniStore` _(carry-over 3A)_
- [x] Tombol Import Excel + modal upload + preview error validasi
- [x] Komponen `ExportButton.vue` (dropdown: Excel/PDF, ukuran A4/F4)
- [x] Halaman `/alumni/permohonan` — list permohonan + form buat permohonan
- [x] Halaman `/admin/permohonan-alumni` — tabel permohonan + approve/reject

**Checkpoint Resmi Penutupan 3B — 2026-06-06:**
> ✅ Semua task 3B (Backend + Frontend) telah selesai dalam 5 batch commit.
> ✅ Carry-over frontend 7 item dari 3A berhasil diselesaikan di 3B (Batch 1–3).
> ✅ Import/Export Alumni (Excel + PDF A4/F4) selesai di Batch 4.
> ✅ AlumniRequest full-stack (Backend + Frontend Admin + Alumni) selesai di Batch 5.
> ✅ Phase Overview diupdate: Phase 3 Selesai = 2 session.
> 🔒 **Session 3B RESMI DITUTUP. Lanjut ke Session 3C.**

**Catatan Sesi 3B:**
> Session 3B diselesaikan dalam 5 batch pada 2026-06-06.
> Batch 1: useAlumniStore + AlumniListPage (halaman daftar alumni admin dengan filter lengkap).
> Batch 2: AlumniDetailPage + AlumniEmploymentTab + AlumniCard.vue + AlumniFormStepper (3-step form).
> Batch 3: AlumniProfilePage (self) + AlumniEmploymentPage (self) + integrasi ke router.
> Batch 4: AlumniImport (Laravel Excel, validasi baris per baris) + AlumniExport (Excel+PDF A4/F4) + 3 endpoint baru + ImportModal.vue + ExportButton.vue.
> Batch 5: AlumniRequest full-stack — Repository + Service + 2 Controller + Request + Policy + Resource + 2 halaman Vue (admin + alumni self).
> Seluruh frontend carry-over 3A tuntas. Phase 3 kini 2/3 session selesai.

---

### SESSION 3C — Employment Tracking
**Status:** 🔄 AKTIF — Dimulai 2026-06-06

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

- [ ] `EmployerAccessService` — create token + revoke + list per tracer study
- [ ] `EmployerAccessController` — generate, revoke, list
- [ ] Endpoint: `POST /api/v1/admin/tracer-studies/:id/employer-tokens`
- [ ] Endpoint: `GET  /api/v1/admin/tracer-studies/:id/employer-tokens`
- [ ] Endpoint: `DELETE /api/v1/admin/employer-tokens/:id`
- [ ] OTP resend endpoint untuk alumni yang token kadaluarsa
- [ ] Queue Job: `SendAlumniInvitationJob` — kirim WhatsApp/Email ke alumni

#### Frontend Tasks

- [ ] Tab "Token Employer" di halaman detail Tracer Study
- [ ] Form generate token (nama perusahaan, email/WA)
- [ ] List token dengan status (aktif/expired/digunakan)
- [ ] Tombol revoke token dengan konfirmasi
- [ ] Halaman `/admin/undangan-alumni` — monitoring undangan terkirim

**Catatan Sesi 5B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 5C — Monitoring & Dashboard Respons

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Endpoint: `GET /api/v1/admin/tracer-studies/:id/response-rate` — tingkat respons real-time
- [ ] Endpoint: `GET /api/v1/admin/tracer-studies/:id/summary` — ringkasan per pertanyaan
- [ ] Export PDF laporan per tracer study

#### Frontend Tasks

- [ ] Dashboard monitoring respons real-time
- [ ] Chart tingkat respons per prodi / per angkatan
- [ ] Komponen `ResponseRateGauge.vue`
- [ ] Komponen `QuestionSummaryChart.vue`
- [ ] Tombol export laporan PDF

**Catatan Sesi 5C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 6 — NOTIFIKASI & INTEGRASI

**Tujuan:** Integrasi WhatsApp Gateway, email notifications, queue jobs.
**Prasyarat:** Phase 5 selesai.

### SESSION 6A — WhatsApp Gateway
**Status:** ⬜ Belum Dimulai
### SESSION 6B — Email Notifications
**Status:** ⬜ Belum Dimulai
### SESSION 6C — Queue & Scheduling
**Status:** ⬜ Belum Dimulai

---

## PHASE 7 — PELAPORAN & ANALITIK

**Tujuan:** Laporan statistik, grafik, export data komprehensif.
**Prasyarat:** Phase 6 selesai.

### SESSION 7A — Statistik & Grafik
**Status:** ⬜ Belum Dimulai
### SESSION 7B — Export Laporan
**Status:** ⬜ Belum Dimulai
### SESSION 7C — Dashboard Analitik
**Status:** ⬜ Belum Dimulai

---

## PHASE 8 — PENGATURAN & KEAMANAN

**Tujuan:** Manajemen pengaturan lanjutan, keamanan sistem, backup.
**Prasyarat:** Phase 7 selesai.

### SESSION 8A — Pengaturan Lanjutan
**Status:** ⬜ Belum Dimulai
### SESSION 8B — Keamanan & Hardening
**Status:** ⬜ Belum Dimulai
### SESSION 8C — Backup & Recovery
**Status:** ⬜ Belum Dimulai

---

## PHASE 9 — TESTING & DEPLOYMENT

**Tujuan:** Unit test, integration test, staging, production deployment.
**Prasyarat:** Phase 8 selesai.

### SESSION 9A — Unit & Feature Tests
**Status:** ⬜ Belum Dimulai
### SESSION 9B — Integration Tests & Staging
**Status:** ⬜ Belum Dimulai
### SESSION 9C — Production Deployment
**Status:** ⬜ Belum Dimulai
