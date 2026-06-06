# Changelog — Tracer Study UNISYA

---

## [Phase 2B Session B] — 2026-06-06

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
- `08_PHASE_TRACKER.md` — Mark Phase 2B COMPLETE
- `09_CHANGELOG.md` — Update changelog

**UI/UX Notes:**
- Semua halaman konsisten dengan pola Phase 2A (search + table + pagination + modal)
- `ProfessionsPage` memiliki filter tambahan by kategori profesi (dropdown)
- `InstitutionsPage` memiliki filter tambahan by tipe institusi (dropdown)
- `ProfessionFormModal` load dropdown kategori via `fetchAllCategories()` on mount
- `InstitutionFormModal` list tipe: perusahaan, instansi, pendidikan, wirausaha, ngo, lainnya
- Route names Phase 2B menggunakan naming convention `admin.{resource}` (dot notation) konsisten dengan Phase 2A

---

## [Phase 2B Session A] — 2026-06-06

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
- `resources/js/router/index.js` — Register routes Phase 2A

---

## [Phase 2A Session B — Backend] — 2026-06-04

### Backend

**Files Created:**
- `app/Repositories/FakultasRepository.php`
- `app/Services/FakultasService.php`
- `app/Http/Controllers/Admin/FakultasController.php`
- `app/Http/Requests/Admin/StoreFakultasRequest.php`
- `app/Http/Requests/Admin/UpdateFakultasRequest.php`
- `app/Http/Resources/Admin/FakultasResource.php`
- `app/Policies/FakultasPolicy.php`
- *(idem untuk StudyProgram dan User)*
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
- Laravel 12 + PHP 8.3 installed
- Vue 3 + Vite + Pinia + Vue Router configured
- Base layouts: AdminLayout, AlumniLayout, EmployerLayout
- Base components: AppButton, AppInput, AppModal, AppTable, AppPagination, AppBadge, AppConfirm
- Auth: Sanctum SPA + OTP flow
- Stores: auth.js, ui.js
- Login, OTP, Dashboard pages
