# 08 — PHASE TRACKER

> **Single source of truth** untuk status pengerjaan seluruh phase.
> Update wajib setelah setiap session selesai.

---

## STATUS RINGKASAN

| Phase | Nama | Status | Progress |
|-------|------|--------|----------|
| 1 | Auth & Core Infrastructure | ✅ SELESAI | 100% |
| 2A | Master Data — User, Fakultas, Prodi | ✅ SELESAI | 100% |
| 2B | Master Data — Profesi, Institusi | ✅ SELESAI | 100% |
| 2C | Master Data — Kuesioner | ⏳ Pending | 0% |
| 3 | Alumni Module | ⏳ Pending | 0% |
| 4 | Tracer Study Module | ⏳ Pending | 0% |
| 5 | Employer Module | ⏳ Pending | 0% |
| 6 | Laporan & Analitik | ⏳ Pending | 0% |

---

## PHASE 1 — Auth & Core Infrastructure ✅

### Session 1A — Backend Foundation ✅
**Tanggal selesai:** Mei 2026

**Deliverables:**
- [x] Migration: users, otp_verifications, employer_accesses, personal_access_tokens
- [x] Model: User, OtpVerification, EmployerAccess (UUID, soft delete, fillable, casts)
- [x] Service: AuthService, OtpService
- [x] Repository: UserRepository
- [x] Controller: LoginController, OtpController, EmployerAccessController
- [x] Request: LoginRequest, OtpRequestRequest, OtpVerifyRequest, EmployerOtpRequestRequest, EmployerOtpVerifyRequest
- [x] Resource: UserResource
- [x] Policy: AuthServiceProvider (Gate admin, alumni)
- [x] Middleware: EnsureUserIsActive
- [x] Routes: api.php (auth, employer, admin, alumni)
- [x] Seeder: RoleSeeder (super_admin user)

### Session 1B — Frontend Foundation ✅
**Tanggal selesai:** Mei 2026

**Deliverables:**
- [x] Vue 3 + Vite + Pinia + Vue Router setup
- [x] Tailwind CSS konfigurasi
- [x] Store: useAuthStore, useUIStore
- [x] Layout: AdminLayout, AlumniLayout, AuthLayout, EmployerLayout
- [x] Component: AppSidebar, AppButton, AppInput, AppModal, AppSelect, AppBadge, AppSkeleton, AppConfirm
- [x] Pages: LoginPage, OtpPage, DashboardPage (admin), DashboardPage (alumni), employer pages
- [x] Router: index.js lengkap dengan guards
- [x] HTTP: lib/http.js (axios + interceptor)

### Session 1C — Security Patch ✅
**Tanggal selesai:** Juni 2026

**Deliverables:**
- [x] Patch super_admin konsistensi di LoginController
- [x] Patch OtpController field mismatch (otp_code vs otp)
- [x] Patch User model $fillable
- [x] Patch OtpVerification model $fillable
- [x] AuthService: tambah requestEmployerOtp, verifyEmployerOtp
- [x] EmployerAccessController: inject AuthService dengan benar

---

## PHASE 2A — Master Data: User, Fakultas, Prodi ✅

### Session 2A-1 — Backend ✅
**Tanggal selesai:** Juni 2026

**Deliverables:**
- [x] Migration: faculties, study_programs
- [x] Model: Faculty, StudyProgram
- [x] Factory: FacultyFactory, StudyProgramFactory
- [x] Seeder: FacultySeeder, StudyProgramSeeder, DatabaseSeeder update
- [x] Repository: FacultyRepository, StudyProgramRepository, UserRepository
- [x] Service: FacultyService, StudyProgramService, UserService
- [x] Controller: FacultyController, StudyProgramController, UserController
- [x] Request: StoreFacultyRequest, UpdateFacultyRequest, StoreStudyProgramRequest, UpdateStudyProgramRequest, StoreUserRequest, UpdateUserRequest, ResetPasswordRequest
- [x] Resource: FacultyResource, StudyProgramResource, UserResource
- [x] Policy: FacultyPolicy, StudyProgramPolicy, UserPolicy
- [x] Routes: api.php (admin/faculties, admin/study-programs, admin/users)

### Session 2A-2 — Frontend ✅
**Tanggal selesai:** 06 Juni 2026

**Deliverables:**
- [x] Store: useUserStore (meta key, fixed API names)
- [x] Store: useFacultyStore (meta key, correct action names)
- [x] Store: useStudyProgramStore (meta key, studyPrograms state, correct action names)
- [x] Component: AppTable.vue
- [x] Component: AppPagination.vue
- [x] Page: users/UsersPage.vue
- [x] Page: users/UserFormModal.vue
- [x] Page: faculties/FacultiesPage.vue
- [x] Page: faculties/FacultyFormModal.vue
- [x] Page: study-programs/StudyProgramsPage.vue
- [x] Page: study-programs/StudyProgramFormModal.vue
- [x] Router: routes pengguna, fakultas, program-studi

---

## PHASE 2B — Master Data: Profesi & Institusi ✅ SELESAI

### Pre-condition Check
- [x] Phase 2A 100% selesai
- [x] Semua store API konsisten (pagination→meta, action names aligned)
- [x] Router Phase 2A terdaftar
- [x] Naming convention table sudah didokumentasikan

### Session 2B — Backend ✅
**Tanggal selesai:** 06 Juni 2026

**Deliverables:**
- [x] Migration: `profession_categories`, `professions`, `institutions` (sudah ada dari sesi sebelumnya)
- [x] Model: ProfessionCategory, Profession, Institution (sudah ada dari sesi sebelumnya)
- [x] Factory: ProfessionCategoryFactory, ProfessionFactory, InstitutionFactory
- [x] Seeder: ProfessionCategorySeeder, ProfessionSeeder, InstitutionSeeder
- [x] Repository: ProfessionCategoryRepository, ProfessionRepository, InstitutionRepository
- [x] Service: ProfessionCategoryService, ProfessionService, InstitutionService
- [x] Controller: ProfessionCategoryController, ProfessionController, InstitutionController
- [x] Request: Store/UpdateProfessionCategoryRequest, Store/UpdateProfessionRequest, Store/UpdateInstitutionRequest
- [x] Resource: ProfessionCategoryResource, ProfessionResource, InstitutionResource
- [x] Policy: ProfessionCategoryPolicy, ProfessionPolicy, InstitutionPolicy
- [x] Routes: api.php — routes 2B sudah terdaftar (pre-existing)

### Session 2B — Frontend ⏳ PENDING

- [ ] Store: useProfessionCategoryStore
- [ ] Store: useProfessionStore
- [ ] Store: useInstitutionStore
- [ ] Page: profession-categories/ProfessionCategoriesPage.vue
- [ ] Page: profession-categories/ProfessionCategoryFormModal.vue
- [ ] Page: professions/ProfessionsPage.vue
- [ ] Page: professions/ProfessionFormModal.vue
- [ ] Page: institutions/InstitutionsPage.vue
- [ ] Page: institutions/InstitutionFormModal.vue
- [ ] Router: ganti PlaceholderPage dengan real pages untuk routes kategori-profesi, profesi, institusi

### Dependencies Phase 2B
- `profession_categories` → `professions` (FK: profession_category_id)
- `professions` dan `institutions` → digunakan di Phase 3 (Alumni employment data)

### Risks Phase 2B
- Tidak ada risk tinggi; semua tabel independen dari data Phase 2A
- `InstitutionDetail` dan `updateDetail` endpoint → placeholder 501, implementasi Phase 2B Frontend atau Phase berikutnya

---

## PHASE 2C — Master Data: Kuesioner ⏳

**Belum dimulai. Menunggu Phase 2B Frontend selesai.**

Scope:
- Kategori Kuesioner
- Tipe Jawaban
- Kuesioner & Pertanyaan
- Pilihan Jawaban

---

## PHASE 3 — Alumni Module ⏳

**Belum dimulai. Menunggu Phase 2 selesai.**

---

## PHASE 4 — Tracer Study Module ⏳

**Belum dimulai.**

---

## PHASE 5 — Employer Module ⏳

**Belum dimulai.**

---

## PHASE 6 — Laporan & Analitik ⏳

**Belum dimulai.**

---

## NAMING CONVENTIONS (Wajib Ikuti Semua Phase)

### Store State Keys

| Resource | State List | State Meta | State All (dropdown) |
|---|---|---|---|
| User | `users` | `meta` | — |
| Faculty | `faculties` | `meta` | `allFaculties` |
| StudyProgram | `studyPrograms` | `meta` | `allPrograms` |
| ProfessionCategory | `professionCategories` | `meta` | `allCategories` |
| Profession | `professions` | `meta` | `allProfessions` |
| Institution | `institutions` | `meta` | `allInstitutions` |

### Store Action Names

| Resource | fetch list | fetch all | create | update | delete | restore |
|---|---|---|---|---|---|---|
| User | `fetchUsers` | — | `createUser` | `updateUser` | `deleteUser` | — |
| Faculty | `fetchFaculties` | `fetchAllFaculties` | `createFaculty` | `updateFaculty` | `deleteFaculty` | `restoreFaculty` |
| StudyProgram | `fetchStudyPrograms` | `fetchAllPrograms` | `createStudyProgram` | `updateStudyProgram` | `deleteStudyProgram` | `restoreStudyProgram` |
| ProfessionCategory | `fetchProfessionCategories` | `fetchAllCategories` | `createProfessionCategory` | `updateProfessionCategory` | `deleteProfessionCategory` | `restoreProfessionCategory` |
| Profession | `fetchProfessions` | `fetchAllProfessions` | `createProfession` | `updateProfession` | `deleteProfession` | `restoreProfession` |
| Institution | `fetchInstitutions` | `fetchAllInstitutions` | `createInstitution` | `updateInstitution` | `deleteInstitution` | `restoreInstitution` |

### Meta Shape (Standard Pagination)

```js
meta = {
  current_page: 1,
  last_page:    1,
  total:        0,
  from:         0,
  to:           0,
  per_page:     15,
}
```

---

*Terakhir diupdate: 06 Juni 2026 — Phase 2B Backend SELESAI ✅, Phase 2B Frontend PENDING ⏳*
