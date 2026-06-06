# 09 — CHANGELOG

> Semua perubahan signifikan dicatat di sini secara kronologis.
> Format: `[YYYY-MM-DD] Phase · Session — Deskripsi`

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
- **`FacultyFormModal.vue` baris 11**: `const store = useUserStore()` tanpa import → akan crash `ReferenceError`. Developer sudah mengkomentari baris tersebut. Store fix ini memastikan tidak ada referensi stale di tempat lain.
- **Semua store**: mismatch key `pagination` vs `meta` yang dipakai di Pages → diseragamkan ke `meta`
- **`useStudyProgramStore`**: Pages memanggil `store.studyPrograms` dan `store.fetchStudyPrograms()` tapi store meng-expose `programs` dan `fetchPrograms` → diseragamkan

### Database Changes
- Tidak ada perubahan schema di session ini
- Factory baru: `FacultyFactory`, `StudyProgramFactory` (untuk testing)

### UI Changes
- Tiga halaman admin baru tersedia: Pengguna, Fakultas, Program Studi
- Dua base component baru: `AppTable` (skeleton + empty state), `AppPagination`
- Router Phase 2A terdaftar dan aktif

### Security Changes
- Tidak ada perubahan security di session ini

### Refactoring Notes
- Semua store kini menggunakan `meta` (bukan `pagination`) sebagai standard pagination state key
- Naming convention tabel ditambahkan di `08_PHASE_TRACKER.md` sebagai acuan wajib untuk semua phase berikutnya

---

## [2026-06-05] Phase 2A · Session 2A-1 — Backend Master Data

### Files Created
- `database/migrations/*_create_faculties_table.php`
- `database/migrations/*_create_study_programs_table.php`
- `app/Models/Faculty.php`
- `app/Models/StudyProgram.php`
- `app/Repositories/FacultyRepository.php`
- `app/Repositories/StudyProgramRepository.php`
- `app/Repositories/UserRepository.php` (update)
- `app/Services/FacultyService.php`
- `app/Services/StudyProgramService.php`
- `app/Services/UserService.php`
- `app/Http/Controllers/Api/Admin/FacultyController.php`
- `app/Http/Controllers/Api/Admin/StudyProgramController.php`
- `app/Http/Controllers/Api/Admin/UserController.php`
- `app/Http/Requests/Admin/StoreFacultyRequest.php`
- `app/Http/Requests/Admin/UpdateFacultyRequest.php`
- `app/Http/Requests/Admin/StoreStudyProgramRequest.php`
- `app/Http/Requests/Admin/UpdateStudyProgramRequest.php`
- `app/Http/Requests/Admin/StoreUserRequest.php`
- `app/Http/Requests/Admin/UpdateUserRequest.php`
- `app/Http/Requests/Admin/ResetPasswordRequest.php`
- `app/Http/Resources/FacultyResource.php`
- `app/Http/Resources/StudyProgramResource.php`
- `app/Http/Resources/UserResource.php`
- `app/Policies/FacultyPolicy.php`
- `app/Policies/StudyProgramPolicy.php`
- `app/Policies/UserPolicy.php`
- `database/seeders/FacultySeeder.php`
- `database/seeders/StudyProgramSeeder.php`

### Files Modified
- `routes/api.php` — Tambah routes admin: faculties, study-programs, users

### Database Changes
- Tabel baru: `faculties` (uuid PK, name, code, is_active, soft delete)
- Tabel baru: `study_programs` (uuid PK, faculty_id FK, name, code, degree, is_active, soft delete)

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

*Terakhir diupdate: 06 Juni 2026*
