# Changelog — Tracer Study UNISYA

> File ini mengikuti `08_PHASE_TRACKER.md` sebagai sumber kebenaran tunggal.
> Setiap entri changelog harus selaras dengan task yang telah di-check `[x]` di Phase Tracker.

---

## [Audit Phase 2A–2C] — 2026-06-06

### Audit & Dokumentasi

**Hasil Audit:**
- Phase 2A: ✅ COMPLETE — semua task backend & frontend selesai. Pending: Seeder & Factory (carry-over ke 2C).
- Phase 2B: ✅ COMPLETE — semua task backend & frontend selesai. Pending: InstitutionDetail + Tab UI + Seeder (carry-over ke 2C).
- Phase 2C: ⬜ Belum Dimulai — menunggu carry-over dari 2A & 2B ditambahkan ke task list.

**Files Modified:**
- `08_PHASE_TRACKER.md` — Update status Phase 2A & 2B menjadi COMPLETE, centang semua task yang sudah selesai, tandai task pending dengan keterangan, tambah carry-over tasks ke Phase 2C, update konflik & keputusan teknis.
- `09_CHANGELOG.md` — Sinkronisasi dengan Phase Tracker.

**Konflik Terdeteksi & Dicatat:**
- C-01: Seeder Fakultas & StudyProgram belum ada → carry-over ke Phase 2C
- C-02: InstitutionDetailController belum ada → carry-over ke Phase 2C
- C-03: Tab Detail Institusi (frontend) belum ada → carry-over ke Phase 2C

---

## [Phase 2B Session B — Frontend] — 2026-06-06

### Frontend

**Files Created:**
- `resources/js/stores/useProfessionCategoryStore.js`
- `resources/js/stores/useProfessionStore.js`
- `resources/js/stores/useInstitutionStore.js`
- `resources/js/pages/admin/profession-categories/ProfessionCategoriesPage.vue`
- `resources/js/pages/admin/profession-categories/ProfessionCategoryFormModal.vue`
- `resources/js/pages/admin/professions/ProfessionsPage.vue`
- `resources/js/pages/admin/professions/ProfessionFormModal.vue`
- `resources/js/pages/admin/institutions/InstitutionsPage.vue`
- `resources/js/pages/admin/institutions/InstitutionFormModal.vue`

**Files Modified:**
- `resources/js/router/index.js` — Replace PlaceholderPage untuk 3 route Phase 2B:
  - `admin.profession-categories` → `ProfessionCategoriesPage.vue`
  - `admin.professions` → `ProfessionsPage.vue`
  - `admin.institutions` → `InstitutionsPage.vue`

**UI/UX Notes:**
- Semua halaman konsisten dengan pola Phase 2A (search + table + pagination + modal)
- `ProfessionsPage` memiliki filter tambahan by kategori profesi (dropdown)
- `InstitutionsPage` memiliki filter tambahan by tipe institusi (dropdown)
- `ProfessionFormModal` load dropdown kategori via `fetchAllCategories()` on mount
- `InstitutionFormModal` list tipe: perusahaan, instansi, pendidikan, wirausaha, ngo, lainnya
- Route names Phase 2B menggunakan naming convention `admin.{resource}` (dot notation) konsisten dengan Phase 2A

---

## [Phase 2B Session A — Backend] — 2026-06-06

### Backend

**Files Created:**
- `database/migrations/2026_06_04_000007_create_profession_categories_table.php`
- `database/migrations/2026_06_04_000008_create_professions_table.php`
- `database/migrations/2026_06_04_000009_create_institutions_table.php`
- `app/Models/ProfessionCategory.php`
- `app/Models/Profession.php`
- `app/Models/Institution.php`
- `app/Repositories/ProfessionCategoryRepository.php`
- `app/Repositories/ProfessionRepository.php`
- `app/Repositories/InstitutionRepository.php`
- `app/Services/ProfessionCategoryService.php`
- `app/Services/ProfessionService.php`
- `app/Services/InstitutionService.php`
- `app/Http/Controllers/Admin/ProfessionCategoryController.php`
- `app/Http/Controllers/Admin/ProfessionController.php`
- `app/Http/Controllers/Admin/InstitutionController.php`
- `app/Http/Requests/Admin/StoreProfessionCategoryRequest.php`
- `app/Http/Requests/Admin/UpdateProfessionCategoryRequest.php`
- `app/Http/Requests/Admin/StoreProfessionRequest.php`
- `app/Http/Requests/Admin/UpdateProfessionRequest.php`
- `app/Http/Requests/Admin/StoreInstitutionRequest.php`
- `app/Http/Requests/Admin/UpdateInstitutionRequest.php`
- `app/Http/Resources/Admin/ProfessionCategoryResource.php`
- `app/Http/Resources/Admin/ProfessionResource.php`
- `app/Http/Resources/Admin/InstitutionResource.php`
- `app/Policies/ProfessionCategoryPolicy.php`
- `app/Policies/ProfessionPolicy.php`
- `app/Policies/InstitutionPolicy.php`

---

## [Phase 2A Session C — Frontend] — 2026-06-04

### Frontend

**Files Created:**
- `resources/js/stores/useFacultyStore.js`
- `resources/js/stores/useStudyProgramStore.js`
- `resources/js/stores/useUserStore.js`
- `resources/js/pages/admin/faculties/FacultiesPage.vue`
- `resources/js/pages/admin/faculties/FacultyFormModal.vue`
- `resources/js/pages/admin/study-programs/StudyProgramsPage.vue`
- `resources/js/pages/admin/study-programs/StudyProgramFormModal.vue`
- `resources/js/pages/admin/users/UsersPage.vue`
- `resources/js/pages/admin/users/UserFormModal.vue`

**Files Modified:**
- `resources/js/router/index.js` — Register routes Phase 2A:
  - `admin.users` → `UsersPage.vue`
  - `admin.faculties` → `FacultiesPage.vue`
  - `admin.study-programs` → `StudyProgramsPage.vue`

---

## [Phase 2A Session B — Backend] — 2026-06-04

### Backend

**Files Created:**
- `app/Repositories/UserRepository.php`
- `app/Services/UserService.php`
- `app/Http/Controllers/Admin/UserController.php`
- `app/Http/Requests/Admin/StoreUserRequest.php`
- `app/Http/Requests/Admin/UpdateUserRequest.php`
- `app/Http/Requests/Admin/ResetPasswordRequest.php`
- `app/Http/Resources/Admin/UserResource.php`
- `app/Policies/UserPolicy.php`
- `app/Repositories/FakultasRepository.php`
- `app/Services/FakultasService.php`
- `app/Http/Controllers/Admin/FakultasController.php`
- `app/Http/Requests/Admin/StoreFakultasRequest.php`
- `app/Http/Requests/Admin/UpdateFakultasRequest.php`
- `app/Http/Resources/Admin/FakultasResource.php`
- `app/Policies/FakultasPolicy.php`
- `app/Repositories/StudyProgramRepository.php`
- `app/Services/StudyProgramService.php`
- `app/Http/Controllers/Admin/StudyProgramController.php`
- `app/Http/Requests/Admin/StoreStudyProgramRequest.php`
- `app/Http/Requests/Admin/UpdateStudyProgramRequest.php`
- `app/Http/Resources/Admin/StudyProgramResource.php`
- `app/Policies/StudyProgramPolicy.php`
- `routes/admin.php`

---

## [Phase 2A Session A — Migration + Model] — 2026-06-04

### Database

**Files Created:**
- `database/migrations/2026_06_04_000004_create_faculties_table.php`
- `database/migrations/2026_06_04_000005_create_study_programs_table.php`
- `app/Models/Fakultas.php`
- `app/Models/StudyProgram.php`

---

## [Phase 1 — Setup & Infrastruktur] — 2026-06-01

### Initial Setup

**Backend:**
- Laravel 12 + PHP 8.3 installed
- Packages: spatie/laravel-permission, spatie/laravel-activitylog, laravel/sanctum
- Base Service, Repository, Resource abstract classes
- Exception Handler JSON response
- Sanctum SPA authentication + OTP flow
- Role-based middleware

**Frontend:**
- Vue 3 + Vite + Pinia + Vue Router configured
- Base layouts: AdminLayout, AlumniLayout, EmployerLayout
- Base components: AppButton, AppInput, AppModal, AppTable, AppPagination, AppBadge, AppConfirm
- HTTP client (axios), auth store, ui store
- Login page, OTP page, Dashboard pages (Admin + Alumni + Employer)
