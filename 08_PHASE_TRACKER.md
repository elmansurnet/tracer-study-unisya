# Phase Tracker — Tracer Study UNISYA

> **Updated:** 2026-06-06  
> **Last Session:** Phase 2B — Session B (Frontend)  
> **Overall Progress:** Phase 2B COMPLETE ✅

---

## ✅ PHASE 1 — Setup & Infrastruktur (COMPLETE)

### Session A — Laravel Foundation
- [x] Install Laravel 12, PHP 8.3
- [x] Configure `.env`, database MySQL 8
- [x] Install packages: spatie/laravel-permission, spatie/laravel-activitylog, laravel/sanctum
- [x] Base Service, Repository, Resource classes
- [x] Exception Handler JSON response

### Session B — Vue 3 Foundation
- [x] Install Vue 3, Vite, Pinia, Vue Router
- [x] AdminLayout, AlumniLayout, EmployerLayout
- [x] Base components: AppButton, AppInput, AppModal, AppTable, AppPagination, AppBadge, AppConfirm
- [x] HTTP client (axios), auth store, ui store
- [x] Login page, OTP page, Dashboard pages

### Session C — Auth & Security
- [x] Sanctum SPA authentication
- [x] OTP login flow
- [x] Role-based middleware
- [x] CSRF protection

---

## ✅ PHASE 2A — Master Data: Users, Fakultas, Program Studi (COMPLETE)

### Session A — Migration + Model
- [x] Migration: `users` (sudah ada dari Laravel default + roles)
- [x] Migration: `faculties` (`2026_06_04_000004`)
- [x] Migration: `study_programs` (`2026_06_04_000005`)
- [x] Model: `Fakultas` (HasUlids, SoftDeletes, scopes, relations)
- [x] Model: `StudyProgram` (HasUlids, SoftDeletes, belongsTo Faculty)
- [x] Model: `User` (update relations)

### Session B — Backend (Repository, Service, Controller, Request, Resource, Policy)
- [x] FakultasRepository + FakultasService
- [x] StudyProgramRepository + StudyProgramService
- [x] UserRepository + UserService
- [x] Admin\FakultasController (CRUD + all + restore)
- [x] Admin\StudyProgramController (CRUD + all + restore)
- [x] Admin\UserController (CRUD)
- [x] Request classes: StoreFakultasRequest, UpdateFakultasRequest, StoreStudyProgramRequest, UpdateStudyProgramRequest, StoreUserRequest, UpdateUserRequest
- [x] Resource classes: FakultasResource, StudyProgramResource, UserResource
- [x] Policy: FakultasPolicy, StudyProgramPolicy, UserPolicy
- [x] Routes: `routes/admin.php`

### Session C — Frontend
- [x] useFacultyStore.js
- [x] useStudyProgramStore.js
- [x] useUserStore.js
- [x] FacultiesPage.vue + FacultyFormModal.vue
- [x] StudyProgramsPage.vue + StudyProgramFormModal.vue
- [x] UsersPage.vue + UserFormModal.vue
- [x] Router registration (admin.faculties, admin.study-programs, admin.users)

---

## ✅ PHASE 2B — Master Data: Profesi & Institusi (COMPLETE)

### Session A — Migration + Model + Backend
- [x] Migration: `profession_categories` (`2026_06_04_000007`)
- [x] Migration: `professions` (`2026_06_04_000008`)
- [x] Migration: `institutions` (`2026_06_04_000009`)
- [x] Model: `ProfessionCategory` (HasUlids, SoftDeletes, scopes, hasMany Profession)
- [x] Model: `Profession` (HasUlids, SoftDeletes, belongsTo ProfessionCategory)
- [x] Model: `Institution` (HasUlids, SoftDeletes, scopes)
- [x] Repository: ProfessionCategoryRepository, ProfessionRepository, InstitutionRepository
- [x] Service: ProfessionCategoryService, ProfessionService, InstitutionService
- [x] Controller: Admin\ProfessionCategoryController, Admin\ProfessionController, Admin\InstitutionController
- [x] Request classes: StoreProfessionCategoryRequest, UpdateProfessionCategoryRequest, StoreProfessionRequest, UpdateProfessionRequest, StoreInstitutionRequest, UpdateInstitutionRequest
- [x] Resource classes: ProfessionCategoryResource, ProfessionResource, InstitutionResource
- [x] Policy: ProfessionCategoryPolicy, ProfessionPolicy, InstitutionPolicy
- [x] Routes registered in `routes/admin.php`

### Session B — Frontend
- [x] useProfessionCategoryStore.js
- [x] useProfessionStore.js
- [x] useInstitutionStore.js
- [x] ProfessionCategoriesPage.vue + ProfessionCategoryFormModal.vue
- [x] ProfessionsPage.vue + ProfessionFormModal.vue (filter by kategori)
- [x] InstitutionsPage.vue + InstitutionFormModal.vue (filter by tipe)
- [x] Router registration: admin.profession-categories, admin.professions, admin.institutions
- [x] Sidebar navigation links (via existing AdminLayout)

---

## 🔲 PHASE 3 — Alumni Management

### Session A — Migration + Model
- [ ] Migration: `alumni` (sudah ada `2026_06_04_000010` — verify kolom)
- [ ] Model: `Alumni` (HasUlids, SoftDeletes, relations ke Fakultas, StudyProgram, Profession, Institution)
- [ ] Migration: `alumni_employment_histories` (`2026_06_04_000011`)
- [ ] Model: `AlumniEmploymentHistory`

### Session B — Backend
- [ ] AlumniRepository + AlumniService
- [ ] Admin\AlumniController (CRUD + import/export)
- [ ] Request + Resource + Policy untuk Alumni
- [ ] Admin endpoint: `GET /admin/alumni`, `POST /admin/alumni`, `PUT /admin/alumni/{id}`, `DELETE /admin/alumni/{id}`
- [ ] Import alumni via CSV/Excel

### Session C — Frontend
- [ ] useAlumniStore.js
- [ ] AlumniPage.vue (tabel + filter multi-dimensi: fakultas, prodi, angkatan, status kerja)
- [ ] AlumniFormModal.vue
- [ ] AlumniDetailPage.vue (profil lengkap + riwayat kerja)
- [ ] Import modal

---

## 🔲 PHASE 4 — Kuesioner & Tracer Study

*(Belum dimulai — menunggu Phase 3 selesai)*

---

## 🔲 PHASE 5 — Laporan & Analitik

*(Belum dimulai)*

---

## Risk Register

| Risk | Severity | Mitigation |
|---|---|---|
| Import alumni duplikasi | Medium | Unique check NIM/email sebelum insert |
| Relasi profesi null saat alumni diisi manual | Low | Nullable FK + fallback display |
| Performance query tracer study besar | Medium | Eager load + pagination + index DB |
