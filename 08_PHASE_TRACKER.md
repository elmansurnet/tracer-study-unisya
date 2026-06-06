# 08 — PHASE TRACKER

> **Single source of truth** untuk status pengerjaan project Tracer Study UNISYA.
> Diperbarui setiap akhir session.

---

## 📦 PHASE OVERVIEW

| Phase | Nama                         | Status         |
|-------|------------------------------|----------------|
| 1     | Foundation & Auth            | ✅ SELESAI     |
| 2A    | Master Data: Fakultas & Prodi | ✅ SELESAI     |
| 2B    | Master Data: Profesi & Institusi | ✅ SELESAI  |
| 2C    | Audit, Notifikasi & Pengaturan | ✅ SELESAI   |
| 3A    | Alumni: Model, Migration, Repository, Service | ⬜ BELUM |
| 3B    | Alumni: Controller, Resource, Request, Policy | ⬜ BELUM |
| 3C    | Alumni: Frontend (Pages + Stores) | ⬜ BELUM |
| 4     | Survey & Kuesioner           | ⬜ BELUM       |
| 5     | Dashboard & Laporan          | ⬜ BELUM       |
| 6     | Notifikasi WA & Scheduler    | ⬜ BELUM       |
| 7     | Testing & QA                 | ⬜ BELUM       |

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

## 📋 PHASE 3A — Alumni: Model, Migration, Repository, Service
**Status:** ⬜ BELUM DIMULAI  

**Task:**
- [ ] Migration: `create_alumni_table` (sudah ada — verifikasi ulang)
- [ ] Migration: `create_alumni_employment_histories_table`
- [ ] Model: `Alumni` (HasUlids, SoftDeletes, BelongsTo User & StudyProgram)
- [ ] Model: `AlumniEmploymentHistory` (BelongsTo Alumni, Institution, Profession)
- [ ] `AlumniRepository`
- [ ] `AlumniService`
- [ ] `AlumniEmploymentHistoryRepository`
- [ ] `AlumniEmploymentHistoryService`

**Dependencies:**
- Phase 2A: StudyProgram ✅
- Phase 2B: Institution, Profession ✅

---

## 📝 CATATAN KONFLIK YANG DISELESAIKAN

| Kode  | Konflik                            | Resolusi                               |
|-------|------------------------------------|----------------------------------------|
| C-01  | Phase Tracker 2B vs permintaan "Angkatan & Alumni" | Ikuti Tracker; Alumni masuk Phase 3 |
| C-02  | InstitutionDetail carry-over 2B    | Diselesaikan di Phase 2C              |
| C-03  | Seeder Fakultas/StudyProgram       | Sudah ada di repo sejak 2A            |
| C-04  | SettingService sudah ada           | Digunakan langsung di SettingController|
| C-05  | AppSetting/AuditTrail/InstitutionDetail Model sudah ada | Skip create, langsung buat Stack |
