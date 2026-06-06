# 09 — CHANGELOG

> Riwayat perubahan project Tracer Study UNISYA.
> Format: `[Tanggal] Phase Session — Deskripsi`

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
