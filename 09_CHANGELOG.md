# 09 — CHANGELOG

> Semua perubahan signifikan dicatat di sini secara kronologis.
> Format: `[YYYY-MM-DD] Phase · Session — Deskripsi`

---

## [2026-06-06] Phase 2B · Session Backend — Master Data Profesi & Institusi

### Files Created

**Factory:**
- `database/factories/ProfessionCategoryFactory.php`
- `database/factories/ProfessionFactory.php`
- `database/factories/InstitutionFactory.php`

**Seeder:**
- `database/seeders/ProfessionCategorySeeder.php` — 10 kategori profesi umum UNISYA
- `database/seeders/ProfessionSeeder.php` — ~50 profesi sesuai kategori (lookup by name)
- `database/seeders/InstitutionSeeder.php` — 20 institusi tempat kerja alumni

**Repository:**
- `app/Repositories/ProfessionCategoryRepository.php`
- `app/Repositories/ProfessionRepository.php` — filter tambahan: `profession_category_id`
- `app/Repositories/InstitutionRepository.php` — filter tambahan: `type`

**Service:**
- `app/Services/ProfessionCategoryService.php`
- `app/Services/ProfessionService.php` — unique check per kategori (name + category_id)
- `app/Services/InstitutionService.php` — field tambahan: type, sector, website, logo

**Request:**
- `app/Http/Requests/Admin/StoreProfessionCategoryRequest.php`
- `app/Http/Requests/Admin/UpdateProfessionCategoryRequest.php`
- `app/Http/Requests/Admin/StoreProfessionRequest.php`
- `app/Http/Requests/Admin/UpdateProfessionRequest.php`
- `app/Http/Requests/Admin/StoreInstitutionRequest.php`
- `app/Http/Requests/Admin/UpdateInstitutionRequest.php`

**Resource:**
- `app/Http/Resources/ProfessionCategoryResource.php`
- `app/Http/Resources/ProfessionResource.php` — include `category` via `whenLoaded`
- `app/Http/Resources/InstitutionResource.php` — include `detail` via `whenLoaded`

**Policy:**
- `app/Policies/ProfessionCategoryPolicy.php`
- `app/Policies/ProfessionPolicy.php`
- `app/Policies/InstitutionPolicy.php`

**Controller:**
- `app/Http/Controllers/Api/Admin/ProfessionCategoryController.php`
- `app/Http/Controllers/Api/Admin/ProfessionController.php` — `all()` support filter `profession_category_id`
- `app/Http/Controllers/Api/Admin/InstitutionController.php` — include `showDetail` & `updateDetail` (placeholder 501)

### Files Modified
- `08_PHASE_TRACKER.md` — Phase 2B Backend ditandai ✅ SELESAI
- `09_CHANGELOG.md` — Entry ini

### Database Changes
- Tidak ada perubahan schema (Migration & Model sudah ada sebelumnya)
- Seeder baru siap dijalankan: `ProfessionCategorySeeder`, `ProfessionSeeder`, `InstitutionSeeder`

### API Changes
- Routes sudah terdaftar sebelumnya di `routes/api.php`
- Endpoint baru aktif:
  - `GET|POST /api/v1/admin/profession-categories` + `/{id}` + `/all` + `/{id}/restore`
  - `GET|POST /api/v1/admin/professions` + `/{id}` + `/all` + `/{id}/restore`
  - `GET|POST /api/v1/admin/institutions` + `/{id}` + `/all` + `/{id}/restore` + `/{id}/detail`

### Security Changes
- Semua endpoint dilindungi `auth:sanctum` + `ensure.active` + `can:admin`
- Semua Policy: `isSuperAdmin()` untuk semua aksi (viewAny, view, create, update, delete, restore)
- Request authorize: `$user->isSuperAdmin()`

### Architecture Notes
- `ProfessionCategory` tidak memiliki `code` (berbeda dari Faculty/StudyProgram)
- Unique check `Profession`: kombinasi `name + profession_category_id` (bukan hanya `name`)
- `InstitutionController::updateDetail()` → 501 placeholder; `InstitutionDetail` model implementasi Phase berikutnya
- Semua Service inject `AuditService` dan catat audit log untuk setiap operasi CRUD

---

## [2026-06-06] Phase 2A · Session 2A-2 — Frontend Master Data + Store Fix

### Files Created
- `resources/js/components/base/AppTable.vue` — Reusable table dengan slot, skeleton, empty state
- `resources/js/components/base/AppPagination.vue` — Pagination dengan ellipsis dan info data
- `resources/js/pages/admin/users/UsersPage.vue` — Halaman manajemen pengguna
- `resources/js/pages/admin/users/UserFormModal.vue` — Form create/edit pengguna
- `resources/js/pages/admin/faculties/FacultiesPage.vue` — Halaman manajemen fakultas
- `resources/js/pages/admin/faculties/FacultyFormModal.vue` — Form create/edit fakultas
- `resources/js/pages/admin/study-programs/StudyProgramsPage.vue` — Halaman manajemen program studi
- `resources/js/pages/admin/study-programs/StudyProgramFormModal.vue` — Form create/edit prodi
- `database/factories/FacultyFactory.php`
- `database/factories/StudyProgramFactory.php`

### Files Modified
- `resources/js/stores/useUserStore.js`
  - Rename state `pagination` → `meta` (konsisten dengan Pages)
  - Tambah optimistic update di `toggleActive`
  - Init `meta` dengan default shape lengkap (termasuk `per_page`)
- `resources/js/stores/useFacultyStore.js`
  - Rename state `pagination` → `meta`
  - Tambah optimistic delete di `deleteFaculty`
  - Tambah `fetchAllFaculties` untuk dropdown
  - Init `meta` dengan default shape lengkap
- `resources/js/stores/useStudyProgramStore.js`
  - Rename state `pagination` → `meta`
  - Rename state `programs` → `studyPrograms` (konsisten dengan Pages)
  - Rename actions: `fetchPrograms` → `fetchStudyPrograms`, `createProgram` → `createStudyProgram`, `updateProgram` → `updateStudyProgram`, `deleteProgram` → `deleteStudyProgram`, `restoreProgram` → `restoreStudyProgram`
  - Tambah optimistic delete di `deleteStudyProgram`
  - Init `meta` dengan default shape lengkap
- `resources/js/router/index.js`
  - Tambah 3 routes Phase 2A: `pengguna`, `fakultas`, `program-studi`
- `database/seeders/DatabaseSeeder.php`
  - Daftarkan `FacultySeeder`, `StudyProgramSeeder`
- `08_PHASE_TRACKER.md` — Phase 2A ditutup ✅, Phase 2B dibuka 🟡
- `09_CHANGELOG.md` — Entry ini

### Bug Fixes
- **`FacultyFormModal.vue` baris 11**: `const store = useUserStore()` tanpa import → akan crash `ReferenceError`
- **Semua store**: mismatch key `pagination` vs `meta` → diseragamkan ke `meta`
- **`useStudyProgramStore`**: Pages memanggil `store.studyPrograms` dan `store.fetchStudyPrograms()` → diseragamkan

### Database Changes
- Tidak ada perubahan schema di session ini
- Factory baru: `FacultyFactory`, `StudyProgramFactory`

---

## [2026-06-04] Phase 1C · Security Patch — super_admin Consistency

### Files Modified
- `app/Http/Controllers/Api/Auth/LoginController.php` — Guard super_admin fix
- `app/Http/Controllers/Api/Auth/OtpController.php` — Field otp_code fix
- `app/Http/Controllers/Api/Auth/EmployerAccessController.php` — Inject AuthService
- `app/Services/AuthService.php` — Tambah requestEmployerOtp, verifyEmployerOtp
- `app/Models/User.php` — Lengkapi $fillable
- `app/Models/OtpVerification.php` — Lengkapi $fillable
- `app/Providers/AuthServiceProvider.php` — Verifikasi Gate konsistensi

### Security Changes
- `super_admin` role konsisten dari database hingga response API
- OTP field naming diseragamkan (`otp_code`)
- EmployerAccess tidak lagi bypass AuthService

---

## [2026-05-xx] Phase 1B · Session 1B — Frontend Foundation

### Files Created
- Setup Vue 3, Vite, Pinia, Vue Router, Tailwind CSS
- Store: `useAuthStore`, `useUIStore`
- Layout: `AdminLayout`, `AlumniLayout`, `AuthLayout`, `EmployerLayout`
- Component: `AppSidebar`, `AppButton`, `AppInput`, `AppModal`, `AppSelect`, `AppBadge`, `AppSkeleton`, `AppConfirm`
- Pages: Auth (Login, OTP), Admin Dashboard, Alumni Dashboard, Employer pages
- Router: `index.js` lengkap dengan guards
- HTTP: `lib/http.js`

---

## [2026-05-xx] Phase 1A · Session 1A — Backend Foundation

### Files Created
- Migrations: users, otp_verifications, employer_accesses, personal_access_tokens
- Models: User, OtpVerification, EmployerAccess
- Services: AuthService, OtpService
- Controllers: LoginController, OtpController, EmployerAccessController
- Middleware: EnsureUserIsActive
- Seeder: RoleSeeder
- Routes: api.php initial

---

*Terakhir diupdate: 06 Juni 2026 — Phase 2B Backend SELESAI ✅*
