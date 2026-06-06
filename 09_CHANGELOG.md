# 09 — CHANGELOG

> Riwayat perubahan project Tracer Study UNISYA.
> Format: `[Tanggal] Phase Session — Deskripsi`

---

## [2026-06-06] Phase 3A — CRUD Alumni Dasar (Backend Full-Stack) — COMPLETE

### Overview
Session 3A diselesaikan dalam 4 batch push pada 2026-06-06. Seluruh backend stack Alumni dan AlumniEmploymentHistory selesai: Migration (verifikasi), Model, Repository, Service, Request, Resource, Policy, Controller (Admin + AlumniSelf), AuthServiceProvider, dan routes/api.php.

---

### Batch 1 — Migration Verifikasi + Model + Repository
**Commit:** Migration & Model Alumni sudah ada dari Phase 1 skeleton — diverifikasi + AlumniRepository + AlumniEmploymentHistoryRepository

**Files Created:**
- `app/Models/Alumni.php` — HasUlids, SoftDeletes, audit fields, GENDERS/EMPLOYMENT_STATUSES constants, relasi: `studyProgram`, `user`, `employmentHistories`, `tracerStudies`
- `app/Models/AlumniEmploymentHistory.php` — HasUlids, SoftDeletes, relasi: `alumni`, `institution`, `profession`
- `app/Repositories/AlumniRepository.php` — paginate (multi-filter: search, study_program_id, faculty_id, graduation_year, employment_status, is_employed, gender, with_trashed), findById, findByNim, byStudyProgram, byGraduationYear, countByEmploymentStatus, graduationYears, create, update, softDelete, restore
- `app/Repositories/AlumniEmploymentHistoryRepository.php` — paginate, findById, byAlumni, currentForAlumni, clearCurrentForAlumni, create, update, softDelete, restore

**Files Verified (sudah ada, tidak di-overwrite):**
- `database/migrations/2026_06_04_000010_create_alumni_table.php`
- `database/migrations/2026_06_04_000011_create_alumni_employment_histories_table.php`

---

### Batch 2 — Service Layer
**Commit:** AlumniService + AlumniEmploymentHistoryService

**Files Created:**
- `app/Services/AlumniService.php` — create (NIM unique check + resolve user_id by email), update (partial update), updateEmploymentStatus (atomik), delete (guard: cegah hapus jika ada tracer study), restore, paginate, findOrFail, findOrFailWithTrashed, byStudyProgram, byGraduationYear, countByEmploymentStatus, graduationYears
- `app/Services/AlumniEmploymentHistoryService.php` — create (one-current-job business rule: clearCurrentForAlumni), update (is_current conflict resolution), delete (sync is_employed Alumni), restore (sync is_employed), syncAlumniEmploymentStatus (private helper)

---

### Batch 3 — Requests + Resources + Policies
**Commit:** 8 Request + 2 Resource + 2 Policy

**Files Created (Admin Requests):**
- `app/Http/Requests/Admin/StoreAlumniRequest.php` — authorize `isSuperAdmin()`, validasi: graduation_year max date('Y')+1, ipk max 4.00, birth_date before:today, enum via `Alumni::GENDERS` & `Alumni::EMPLOYMENT_STATUSES`
- `app/Http/Requests/Admin/UpdateAlumniRequest.php` — semua field `sometimes`, partial update
- `app/Http/Requests/Admin/StoreAlumniEmploymentHistoryRequest.php` — end_date `after_or_equal:start_date`, job_relevance enum 4 nilai
- `app/Http/Requests/Admin/UpdateAlumniEmploymentHistoryRequest.php` — semua field `sometimes`

**Files Created (AlumniSelf Requests — folder baru):**
- `app/Http/Requests/AlumniSelf/UpdateProfileRequest.php` — authorize `isAlumni()` + `user->alumni !== null`; hanya field kontak (phone, address, city, province, postal_code, photo) — field akademik tidak bisa diubah sendiri
- `app/Http/Requests/AlumniSelf/UpdateEmploymentRequest.php` — atomik update: employment_status + is_employed + waiting_period_months
- `app/Http/Requests/AlumniSelf/StoreEmploymentHistoryRequest.php`
- `app/Http/Requests/AlumniSelf/UpdateEmploymentHistoryRequest.php`

**Files Created (Resources):**
- `app/Http/Resources/AlumniResource.php` — semua field + `whenLoaded()` untuk studyProgram (+ faculty), user, employment_histories (anti-N+1)
- `app/Http/Resources/AlumniEmploymentHistoryResource.php` — semua field + `whenLoaded()` untuk institution, profession (+ category), alumni

**Files Created (Policies):**
- `app/Policies/AlumniPolicy.php` — viewAny (admin), view (admin/alumni self), create (admin), update (admin), updateSelf (alumni self — cek user_id), delete (admin), restore (admin)
- `app/Policies/AlumniEmploymentHistoryPolicy.php` — viewAny, view, create, update, delete (admin atau alumni owner), restore (admin atau alumni owner)

---

### Batch 4 — Controllers + AuthServiceProvider + Routes
**Commit:** 4 Controllers + AuthServiceProvider update + routes/api.php update

**Files Created (Admin Controllers):**
- `app/Http/Controllers/Api/Admin/AlumniController.php` — 9 method: index (multi-filter), graduationYears, employmentStats (?graduation_year filter), byStudyProgram (nested GET), store, show (eager load full), update, destroy, restore
- `app/Http/Controllers/Api/Admin/AlumniEmploymentHistoryController.php` — 6 method nested di bawah `alumni/{alumniId}`: index, store, show, update, destroy, restore; setiap method validasi kepemilikan `abort_unless(history->alumni_id === alumniId)`

**Files Created (AlumniSelf Controllers — namespace/folder baru):**
- `app/Http/Controllers/Api/AlumniSelf/ProfileController.php` — 3 method: show (load relasi lengkap), update (field kontak saja), updateEmploymentStatus (atomik via AlumniService)
- `app/Http/Controllers/Api/AlumniSelf/EmploymentHistoryController.php` — 6 method self-service; double-guard ownership: `abort_unless(alumni !== null)` + `abort_unless(history->alumni_id === alumni->id, 403)`; restore menggunakan `withTrashed()->find()` sebelum service dipanggil

**Files Modified:**
- `app/Providers/AuthServiceProvider.php` — tambah 2 mapping policy:
  - `Alumni::class => AlumniPolicy::class`
  - `AlumniEmploymentHistory::class => AlumniEmploymentHistoryPolicy::class`
- `routes/api.php` — tambah 24 route baru:
  - Admin: `GET graduation-years`, `GET employment-stats`, `GET/POST /alumni`, `GET/PUT/DELETE/PATCH(restore) /alumni/{id}`, `GET/POST/GET/PUT/DELETE/PATCH(restore) /alumni/{alumniId}/employment-histories`, `GET /study-programs/{id}/alumni`
  - AlumniSelf: `GET/PATCH /alumni/profile`, `PATCH /alumni/employment-status`, `GET/POST/GET/PUT/DELETE/PATCH(restore) /alumni/employment-histories`

---

### Database Changes
- Tidak ada migration baru — migration alumni sudah ada dari Phase 1 skeleton dan sudah diverifikasi lengkap

### API Changes — Admin
- `GET    /api/v1/admin/alumni` — daftar alumni (filter: search, study_program_id, faculty_id, graduation_year, employment_status, is_employed, gender, with_trashed)
- `POST   /api/v1/admin/alumni` — tambah alumni baru
- `GET    /api/v1/admin/alumni/graduation-years` — list tahun wisuda (dropdown)
- `GET    /api/v1/admin/alumni/employment-stats` — statistik status pekerjaan
- `GET    /api/v1/admin/alumni/{id}` — detail alumni + relasi lengkap
- `PUT    /api/v1/admin/alumni/{id}` — update alumni (partial)
- `DELETE /api/v1/admin/alumni/{id}` — soft delete (guard: ada tracer study)
- `PATCH  /api/v1/admin/alumni/{id}/restore` — restore alumni
- `GET    /api/v1/admin/study-programs/{studyProgramId}/alumni` — alumni per prodi
- `GET    /api/v1/admin/alumni/{alumniId}/employment-histories` — list riwayat pekerjaan
- `POST   /api/v1/admin/alumni/{alumniId}/employment-histories` — tambah riwayat
- `GET    /api/v1/admin/alumni/{alumniId}/employment-histories/{id}` — detail riwayat
- `PUT    /api/v1/admin/alumni/{alumniId}/employment-histories/{id}` — update riwayat
- `DELETE /api/v1/admin/alumni/{alumniId}/employment-histories/{id}` — hapus riwayat
- `PATCH  /api/v1/admin/alumni/{alumniId}/employment-histories/{id}/restore` — restore riwayat

### API Changes — Alumni Self-Service
- `GET    /api/v1/alumni/profile` — lihat profil diri sendiri
- `PATCH  /api/v1/alumni/profile` — update field kontak (bukan akademik)
- `PATCH  /api/v1/alumni/employment-status` — update status pekerjaan atomik
- `GET    /api/v1/alumni/employment-histories` — list riwayat pekerjaan milik sendiri
- `POST   /api/v1/alumni/employment-histories` — tambah riwayat baru
- `GET    /api/v1/alumni/employment-histories/{id}` — detail (cek ownership)
- `PUT    /api/v1/alumni/employment-histories/{id}` — update (cek ownership)
- `DELETE /api/v1/alumni/employment-histories/{id}` — hapus (cek ownership)
- `PATCH  /api/v1/alumni/employment-histories/{id}/restore` — restore (cek ownership)

### Security Changes
- `AlumniPolicy` — alumni hanya bisa `viewSelf` dan `updateSelf` data miliknya sendiri; admin bisa semua operasi
- `AlumniEmploymentHistoryPolicy` — alumni hanya bisa CRUD riwayat miliknya; restore untuk alumni owner dan admin
- Double-guard di AlumniSelf controllers: `abort_unless` ownership check SEBELUM Policy (fail-fast, cegah info disclosure)
- `StoreAlumniRequest` / `UpdateAlumniRequest` — otorisasi `isSuperAdmin()` saja, tidak cukup login biasa
- `UpdateProfileRequest` — alumni hanya bisa ubah field kontak, tidak bisa ubah NIM/graduation_year/study_program_id

### Konflik Diselesaikan
- C-06: AlumniSelf controller namespace conflict — gunakan `App\Http\Controllers\Api\AlumniSelf\` terpisah

---

## [2026-06-06] Phase 2C — Audit, Notifikasi & Pengaturan — COMPLETE

### Files Created

**Backend:**
- `app/Repositories/InstitutionDetailRepository.php`
- `app/Services/InstitutionDetailService.php`
- `app/Http/Controllers/Api/Admin/InstitutionDetailController.php`
- `app/Http/Requests/Admin/StoreInstitutionDetailRequest.php`
- `app/Http/Requests/Admin/UpdateInstitutionDetailRequest.php`
- `app/Http/Resources/InstitutionDetailResource.php`
- `app/Policies/InstitutionDetailPolicy.php`
- `app/Http/Controllers/Api/Admin/AuditTrailController.php`
- `app/Http/Controllers/Api/Admin/ActivityLogController.php`
- `app/Http/Controllers/Api/Admin/SettingController.php`
- `app/Observers/AuditTrailObserver.php`
- `app/Policies/AuditTrailPolicy.php`
- `app/Policies/AppSettingPolicy.php`
- `database/seeders/AppSettingSeeder.php`

**Frontend:**
- `resources/js/stores/useAuditTrailStore.js`
- `resources/js/stores/useActivityLogStore.js`
- `resources/js/stores/useSettingStore.js`
- `resources/js/stores/useInstitutionDetailStore.js`
- `resources/js/pages/admin/audit-trail/AuditTrailPage.vue`
- `resources/js/pages/admin/activity-log/ActivityLogPage.vue`
- `resources/js/pages/admin/settings/SettingsPage.vue`
- `resources/js/pages/admin/institutions/InstitutionDetailTab.vue`

### Files Modified
- `app/Providers/AppServiceProvider.php` — daftarkan `AuditTrailObserver` untuk 7 model
- `app/Providers/AuthServiceProvider.php` — daftarkan seluruh policy Phase 2B & 2C
- `database/seeders/DatabaseSeeder.php` — tambah `AppSettingSeeder`
- `routes/api.php` — tambah routes: audit-trail, activity-log, settings, institution detail

### Database Changes
- Tidak ada migration baru (semua tabel sudah ada dari Phase sebelumnya)
- Seeder: `AppSettingSeeder` — 16 setting default di 4 group

### API Changes
- `GET  /api/v1/admin/audit-trails` — list + filter (action, user_id, model, date range)
- `GET  /api/v1/admin/audit-trails/{id}` — detail
- `GET  /api/v1/admin/activity-logs` — list + filter (event, causer, log_name, date range)
- `GET  /api/v1/admin/activity-logs/{id}` — detail
- `DELETE /api/v1/admin/activity-logs` — purge (dengan filter `before` opsional)
- `GET  /api/v1/admin/settings` — list semua setting (grouped)
- `GET  /api/v1/admin/settings/{group}/{key}` — satu setting
- `PUT  /api/v1/admin/settings/{group}/{key}` — update satu setting
- `PUT  /api/v1/admin/settings/batch` — update banyak setting sekaligus
- `GET  /api/v1/admin/institutions/{id}/detail` — pindah ke InstitutionDetailController
- `PUT  /api/v1/admin/institutions/{id}/detail` — upsert detail institusi

### Security Changes
- `AuditTrailPolicy` — hanya `super_admin` yang bisa akses audit trail & purge activity log
- `AppSettingPolicy` — hanya `super_admin` yang bisa lihat & ubah pengaturan
- `InstitutionDetailPolicy` — `admin` dan `operator` bisa view & update
- `AuditTrailObserver` — auto-exclude field sensitif (password, remember_token, api_token)
- `SettingController` — nilai encrypted ditampilkan sebagai `[ENCRYPTED]`

### Konflik Diselesaikan
- C-04: SettingService sudah ada — digunakan langsung
- C-05: AppSetting, AuditTrail, InstitutionDetail model sudah ada — Stack langsung dibuat

---

## [2026-06-06] Phase 2B Session B — Master Data Profesi & Institusi — COMPLETE

### Files Created
- `app/Repositories/ProfessionCategoryRepository.php`
- `app/Services/ProfessionCategoryService.php`
- `app/Http/Controllers/Api/Admin/ProfessionCategoryController.php`
- `app/Http/Requests/Admin/StoreProfessionCategoryRequest.php`
- `app/Http/Requests/Admin/UpdateProfessionCategoryRequest.php`
- `app/Http/Resources/ProfessionCategoryResource.php`
- `app/Policies/ProfessionCategoryPolicy.php`
- `app/Repositories/ProfessionRepository.php`
- `app/Services/ProfessionService.php`
- `app/Http/Controllers/Api/Admin/ProfessionController.php`
- `app/Http/Requests/Admin/StoreProfessionRequest.php`
- `app/Http/Requests/Admin/UpdateProfessionRequest.php`
- `app/Http/Resources/ProfessionResource.php`
- `app/Policies/ProfessionPolicy.php`
- `app/Repositories/InstitutionRepository.php`
- `app/Services/InstitutionService.php`
- `app/Http/Controllers/Api/Admin/InstitutionController.php`
- `app/Http/Requests/Admin/StoreInstitutionRequest.php`
- `app/Http/Requests/Admin/UpdateInstitutionRequest.php`
- `app/Http/Resources/InstitutionResource.php`
- `app/Policies/InstitutionPolicy.php`
- `database/seeders/ProfessionCategorySeeder.php`
- `database/seeders/ProfessionSeeder.php`
- `database/seeders/InstitutionSeeder.php`

### Files Modified
- `routes/api.php` — tambah routes 2B
- `database/seeders/DatabaseSeeder.php` — tambah seeder 2B

---

## [2026-06-06] Phase 2B Session A — Migration + Model Profesi & Institusi — COMPLETE

### Files Created
- `database/migrations/2026_06_04_000006_create_profession_categories_table.php`
- `database/migrations/2026_06_04_000007_create_professions_table.php`
- `database/migrations/2026_06_04_000008_create_institutions_table.php`
- `database/migrations/2026_06_04_000009_create_institution_details_table.php`
- `app/Models/ProfessionCategory.php`
- `app/Models/Profession.php`
- `app/Models/Institution.php`
- `app/Models/InstitutionDetail.php`

---

## [2026-06-04] Phase 2A Session C — Controller + Request + Resource + Policy — COMPLETE

### Files Created
- `app/Http/Controllers/Api/Admin/FacultyController.php`
- `app/Http/Requests/Admin/StoreFacultyRequest.php`
- `app/Http/Requests/Admin/UpdateFacultyRequest.php`
- `app/Http/Resources/FacultyResource.php`
- `app/Policies/FacultyPolicy.php`
- `app/Http/Controllers/Api/Admin/StudyProgramController.php`
- `app/Http/Requests/Admin/StoreStudyProgramRequest.php`
- `app/Http/Requests/Admin/UpdateStudyProgramRequest.php`
- `app/Http/Resources/StudyProgramResource.php`
- `app/Policies/StudyProgramPolicy.php`
- `app/Http/Controllers/Api/Admin/UserController.php`
- `app/Http/Requests/Admin/StoreUserRequest.php`
- `app/Http/Requests/Admin/UpdateUserRequest.php`
- `app/Http/Resources/UserResource.php`
- `app/Policies/UserPolicy.php`

### Files Modified
- `routes/api.php` — tambah routes faculty, study-program, user
- `app/Providers/AuthServiceProvider.php` — daftarkan FacultyPolicy, StudyProgramPolicy, UserPolicy

---

## [2026-06-04] Phase 2A Session B — Repository + Service — COMPLETE

### Files Created
- `app/Repositories/FacultyRepository.php`
- `app/Services/FacultyService.php`
- `app/Repositories/StudyProgramRepository.php`
- `app/Services/StudyProgramService.php`
- `app/Repositories/Contracts/UserRepositoryInterface.php`
- `app/Repositories/Eloquent/UserRepository.php`
- `app/Services/UserService.php`
- `app/Services/AuditService.php`
- `app/Models/AppSetting.php`
- `app/Models/AuditTrail.php`
- `app/Services/SettingService.php`

---

## [2026-06-04] Phase 2A Session A — Migration + Model Fakultas & Prodi — COMPLETE

### Files Created
- `database/migrations/2026_06_04_000004_create_faculties_table.php`
- `database/migrations/2026_06_04_000005_create_study_programs_table.php`
- `app/Models/Faculty.php`
- `app/Models/StudyProgram.php`
- `database/seeders/FacultySeeder.php`
- `database/seeders/StudyProgramSeeder.php`

---

## [2026-06-01] Phase 1 — Foundation & Auth — COMPLETE

### Files Created
- Auth: `LoginController`, `OtpController`, `EmployerAccessController`
- Middleware: `EnsureUserIsActive`, `EnsureEmployerToken`
- Models: `User`, `OtpCode`, `EmployerAccessToken`
- Migrations: users, otp_codes, employer_access_tokens, alumni (skeleton), alumni_employment_histories (skeleton)
- `database/seeders/RoleSeeder.php`
- `app/Services/OtpService.php`
- `app/Providers/AppServiceProvider.php` (rate limiting)
- `app/Providers/AuthServiceProvider.php` (Gate admin, Gate alumni)
