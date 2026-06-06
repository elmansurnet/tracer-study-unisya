# 09 — CHANGELOG

> Riwayat perubahan project Tracer Study UNISYA.
> Format: `[Tanggal] Phase Session — Deskripsi`

---

## [2026-06-06] Phase 3C — RESMI DITUTUP — Employment Tracking Frontend & Autocomplete — CLOSED

### Ringkasan Penutupan Resmi

Session 3C ditutup secara resmi pada 2026-06-06 setelah seluruh Frontend task diselesaikan dan router import error untuk kedua route alumni berhasil di-resolve. Backend employment tracking yang sudah selesai di Session 3A diverifikasi ulang — semua endpoint aktif dan berfungsi tanpa perubahan kode. **Phase 3 (Manajemen Alumni) kini SELESAI PENUH (3/3 session).**

**Verifikasi Penutupan:**
- [x] Semua task Frontend 3C berstatus `[x]` di Phase Tracker
- [x] Backend employment tracking (dari 3A) diverifikasi ulang — tidak ada perubahan kode diperlukan
- [x] Autocomplete institusi terintegrasi di form tambah/edit riwayat pekerjaan
- [x] Autocomplete profesi terintegrasi di form tambah/edit riwayat pekerjaan
- [x] `AlumniEmploymentTab.vue` diverifikasi berfungsi penuh: list, tambah, edit, hapus, restore, tandai current job
- [x] Router import error untuk route `/admin/alumni` dan `/alumni/pekerjaan` resolved (C-07)
- [x] Phase Overview diupdate: Phase 3 Selesai = 3 session, Status = ✅ SELESAI
- [x] `08_PHASE_TRACKER.md` diupdate: 3C SELESAI, Phase 3 SELESAI PENUH, C-07 dicatat
- [x] `09_CHANGELOG.md` diupdate: entry penutupan 3C ditambahkan

---

## [2026-06-06] Phase 3C — Employment Tracking Frontend & Autocomplete — COMPLETE

### Overview
Session 3C diselesaikan pada 2026-06-06. Backend sudah selesai sejak 3A. Session ini fokus verifikasi backend, penyelesaian frontend komponen riwayat pekerjaan, integrasi autocomplete institusi dan profesi, serta resolusi router import error.

---

### Files Modified
- `resources/js/router/index.js` — perbaiki path import komponen untuk route `/admin/alumni` dan `/alumni/pekerjaan` (resolusi C-07: router import error)

### Files Verified (tidak diubah, diverifikasi aktif)
- `resources/js/pages/admin/alumni/AlumniEmploymentTab.vue` — list + form tambah/edit, tandai current job, hapus/restore
- `app/Http/Controllers/Api/Admin/AlumniEmploymentHistoryController.php` — semua endpoint aktif
- `app/Http/Controllers/Api/AlumniSelf/EmploymentHistoryController.php` — semua endpoint aktif

### Database Changes
- Tidak ada migration baru

### API Changes
- Tidak ada endpoint baru — semua endpoint employment tracking sudah aktif sejak 3A

### UI Changes
- Autocomplete institusi: input profesi di form `AlumniEmploymentTab.vue` fetch data dari `GET /api/v1/admin/institutions` dengan debounce search
- Autocomplete profesi: input profesi di form `AlumniEmploymentTab.vue` fetch data dari `GET /api/v1/admin/professions` dengan filter `category_id` opsional
- Router `/admin/alumni` — import path diperbaiki, route aktif
- Router `/alumni/pekerjaan` — import path diperbaiki, route aktif

### Security Changes
- Tidak ada perubahan security — semua policy dan guard sudah aktif sejak 3A

### Bug Fixes
- **C-07:** Router import error — kedua route gagal dimuat karena path komponen Vue salah di `router/index.js`. Diperbaiki dengan menyesuaikan path ke lokasi file aktual komponen.

### Refactoring Notes
- Backend employment tracking tidak perlu refactoring — implementasi 3A sudah production-ready

---

### Ringkasan Perubahan Phase 3C

**Total Files Modified:** 1 file (`router/index.js`)  
**Total Files Verified:** 3 file  
**Migration Baru:** 0  
**API Endpoint Baru:** 0  
**Bug Fixes:** 1 (C-07 router import error)  

---

## [2026-06-06] Phase 3B — RESMI DITUTUP — Import/Export & Permohonan Alumni — CLOSED

### Ringkasan Penutupan Resmi

Session 3B ditutup secara resmi pada 2026-06-06 setelah seluruh task Backend dan Frontend diselesaikan dalam 5 batch commit. Carry-over frontend 7 item dari Session 3A tuntas di Batch 1–3. Fitur Import/Export Alumni (Excel + PDF) selesai di Batch 4. AlumniRequest full-stack (Admin + Alumni Self) selesai di Batch 5.

**Verifikasi Penutupan:**
- [x] Semua task Backend 3B berstatus `[x]` di Phase Tracker
- [x] Semua task Frontend 3B berstatus `[x]` di Phase Tracker (termasuk carry-over 3A)
- [x] 5 Batch commit berhasil push ke `main`
- [x] Carry-over frontend 7 item dari 3A tuntas
- [x] Import (Laravel Excel + validasi baris) + Export (Excel + PDF A4/F4) aktif
- [x] AlumniRequest full-stack: Repository + Service + 2 Controller + Request + Policy + Resource + 2 Vue pages
- [x] Phase Overview diupdate: Phase 3 Selesai = 2 session
- [x] Session 3C status diubah menjadi `🔄 AKTIF`

---

## [2026-06-06] Phase 3B — Import/Export & Permohonan Alumni — COMPLETE

### Overview
Session 3B diselesaikan dalam 5 batch push pada 2026-06-06. Mencakup penyelesaian seluruh carry-over frontend dari Session 3A (Batch 1–3), implementasi Import/Export Alumni (Batch 4), dan AlumniRequest full-stack (Batch 5).

---

### Batch 1 — Frontend Carry-Over 3A: Pinia Store + Halaman Daftar Admin Alumni
**Commit:** useAlumniStore + AlumniListPage (admin)

**Files Created:**
- `resources/js/stores/useAlumniStore.js` — Pinia store: fetchAlumni (multi-filter), fetchAlumniDetail, createAlumni, updateAlumni, deleteAlumni, restoreAlumni, fetchGraduationYears, fetchEmploymentStats, fetchByStudyProgram
- `resources/js/pages/admin/alumni/AlumniListPage.vue` — tabel alumni dengan filter: Fakultas (select), Prodi (select, cascade), Tahun Lulus (select), Status Kerja (select); pagination; tombol tambah, edit, hapus, restore; konfirmasi dialog sebelum hapus/restore

---

### Batch 2 — Frontend Carry-Over 3A: Detail Alumni + Tab Pekerjaan + AlumniCard + Form Stepper
**Commit:** AlumniDetailPage + AlumniEmploymentTab + AlumniCard + AlumniFormStepper

**Files Created:**
- `resources/js/pages/admin/alumni/AlumniDetailPage.vue` — halaman detail alumni: informasi lengkap + tab pekerjaan; breadcrumb; tombol edit, hapus, restore
- `resources/js/pages/admin/alumni/AlumniEmploymentTab.vue` — list riwayat pekerjaan alumni (dalam tab di DetailPage); form tambah/edit riwayat pekerjaan inline; tandai current job; soft delete + restore
- `resources/js/components/alumni/AlumniCard.vue` — komponen card alumni reusable: avatar, nama, NIM, prodi, tahun lulus, badge status kerja; slot action
- `resources/js/pages/admin/alumni/AlumniFormStepper.vue` — form 3 langkah: (1) Data Pribadi, (2) Data Akademik, (3) Status Pekerjaan; validasi per langkah sebelum next; digunakan untuk Buat dan Edit alumni

---

### Batch 3 — Frontend Carry-Over 3A: Halaman Alumni Self-Service
**Commit:** AlumniProfilePage + AlumniEmploymentPage (self-service) + integrasi router

**Files Created:**
- `resources/js/pages/alumni/AlumniProfilePage.vue` — halaman profil diri alumni: tampilkan semua data pribadi + akademik; form edit hanya field kontak (phone, address, city, province, postal_code); upload foto profil; update status pekerjaan atomik
- `resources/js/pages/alumni/AlumniEmploymentPage.vue` — halaman riwayat pekerjaan alumni self-service: list riwayat + form tambah/edit/hapus/restore milik sendiri; badge "Pekerjaan Saat Ini"

**Files Modified:**
- `resources/js/router/index.js` — tambah routes: `/admin/alumni`, `/admin/alumni/:id`, `/alumni/profil`, `/alumni/pekerjaan` dengan guard role yang sesuai

---

### Batch 4 — Import/Export Backend + Frontend
**Commit:** AlumniImport + AlumniExport + AlumniPdfExport + 3 endpoint + ImportModal + ExportButton

**Files Created (Backend):**
- `app/Imports/AlumniImport.php` — Laravel Excel import class: validasi baris per baris (NIM unique, email unique, graduation_year valid, ipk max 4.00, enum fields); collect errors per baris; return `RowImported` event per baris sukses; `WithValidation`, `WithBatchInserts`, `WithChunkReading` (chunk 200)
- `app/Exports/AlumniExport.php` — Laravel Excel export: filter (study_program_id, faculty_id, graduation_year, employment_status); header row; format kolom sesuai tabel alumni; `WithHeadings`, `WithMapping`, `WithStyles`
- `app/Exports/AlumniPdfExport.php` — DomPDF export: template Blade `alumni-export.blade.php`; mendukung ukuran A4 dan F4 via query param `?size=a4|f4`; header universitas dari `app_settings`
- `resources/views/exports/alumni-export.blade.php` — template Blade PDF: tabel alumni dengan header universitas, tanggal cetak, watermark; responsive untuk A4 dan F4
- `app/Http/Controllers/Api/Admin/AlumniImportExportController.php` — 3 method: `import` (handle upload + validasi + return errors), `exportExcel` (stream download), `exportPdf` (stream download)

**Files Created (Frontend):**
- `resources/js/components/alumni/ImportModal.vue` — modal upload Excel: drag-and-drop area; preview nama file; validasi ekstensi .xlsx/.xls client-side; progress upload; tampilkan error per baris jika ada; link download template
- `resources/js/components/alumni/ExportButton.vue` — dropdown button: pilihan Excel / PDF A4 / PDF F4; trigger download via endpoint; loading state per opsi

**Files Modified:**
- `routes/api.php` — tambah 3 route:
  - `POST   /api/v1/admin/alumni/import`
  - `GET    /api/v1/admin/alumni/export/excel`
  - `GET    /api/v1/admin/alumni/export/pdf`
- `resources/js/pages/admin/alumni/AlumniListPage.vue` — integrasi `ImportModal` dan `ExportButton`

**Database Changes:**
- Tidak ada migration baru

**API Changes:**
- `POST   /api/v1/admin/alumni/import` — upload file Excel, return `{imported: N, errors: [{row, field, message}]}`
- `GET    /api/v1/admin/alumni/export/excel` — stream download .xlsx dengan filter opsional
- `GET    /api/v1/admin/alumni/export/pdf` — stream download .pdf, param `?size=a4|f4`

---

### Batch 5 — AlumniRequest Full-Stack (Permohonan Alumni)
**Commit:** AlumniRequest Repository + Service + Controller (Admin+Alumni) + Request + Policy + Resource + 2 Vue pages

**Files Created (Backend):**
- `app/Models/AlumniRequest.php` — HasUlids, SoftDeletes, relasi `alumni`, `reviewer`; STATUSES constants (pending, approved, rejected); scope `pending()`, `byAlumni()`
- `database/migrations/2026_06_04_000012_create_alumni_requests_table.php` — tabel `alumni_requests`: id, alumni_id, type (update_profile/update_employment), payload (JSON), status (enum), notes, reviewed_by, reviewed_at, timestamps, softDeletes
- `app/Repositories/AlumniRequestRepository.php` — paginate (filter: status, alumni_id, type, date_range), findById, pendingForAlumni, create, approve (update status+reviewer+reviewed_at), reject, softDelete
- `app/Services/AlumniRequestService.php` — create (validasi: alumni tidak bisa buat request saat ada pending sejenis), approve (apply payload ke model Alumni atomik), reject, paginate, findOrFail
- `app/Http/Controllers/Api/Admin/AlumniRequestController.php` — 5 method: index (filter+paginate), show, approve, reject, destroy
- `app/Http/Controllers/Api/AlumniSelf/AlumniRequestController.php` — 3 method: index (milik sendiri), store (buat permohonan baru), show (detail milik sendiri)
- `app/Http/Requests/Admin/ApproveAlumniRequestRequest.php` — validasi notes opsional
- `app/Http/Requests/Admin/RejectAlumniRequestRequest.php` — notes wajib saat reject
- `app/Http/Requests/AlumniSelf/StoreAlumniRequestRequest.php` — validasi type + payload sesuai type; cek tidak ada pending request sejenis
- `app/Policies/AlumniRequestPolicy.php` — viewAny/view (admin + alumni self), create (alumni self), approve/reject (admin), delete (admin)
- `app/Http/Resources/AlumniRequestResource.php` — semua field + whenLoaded alumni (nama, nim) + whenLoaded reviewer (nama)

**Files Created (Frontend):**
- `resources/js/pages/alumni/AlumniRequestPage.vue` — halaman alumni self-service: list permohonan milik sendiri dengan status badge; form buat permohonan baru (pilih type, isi payload sesuai type); detail permohonan di modal
- `resources/js/pages/admin/alumni/AlumniRequestAdminPage.vue` — halaman admin: tabel semua permohonan + filter status/type/tanggal; tombol Approve (dengan catatan opsional) dan Reject (catatan wajib); detail diff payload vs data aktual alumni di modal

**Files Modified:**
- `app/Providers/AuthServiceProvider.php` — tambah mapping `AlumniRequest::class => AlumniRequestPolicy::class`
- `routes/api.php` — tambah 8 route Admin + AlumniSelf alumni-requests
- `resources/js/router/index.js` — tambah routes `/alumni/permohonan` dan `/admin/permohonan-alumni`

**Database Changes:**
- Migration baru: `2026_06_04_000012_create_alumni_requests_table.php`

**API Changes (Batch 5):**
- `GET    /api/v1/admin/alumni-requests`
- `GET    /api/v1/admin/alumni-requests/{id}`
- `PATCH  /api/v1/admin/alumni-requests/{id}/approve`
- `PATCH  /api/v1/admin/alumni-requests/{id}/reject`
- `DELETE /api/v1/admin/alumni-requests/{id}`
- `GET    /api/v1/alumni/requests`
- `POST   /api/v1/alumni/requests`
- `GET    /api/v1/alumni/requests/{id}`

**Security Changes (Batch 5):**
- `AlumniRequestPolicy` — alumni hanya bisa lihat dan buat request miliknya; admin melakukan approve/reject
- `AlumniRequestService.create()` — guard: cegah duplikasi request pending sejenis
- `ApproveAlumniRequestRequest` — approve atomik: payload di-apply ke model Alumni dalam satu transaksi DB
- `StoreAlumniRequestRequest` — validasi payload berbeda per type (update_profile vs update_employment)

---

### Ringkasan Perubahan Phase 3B

**Total Files Created:** 25 file baru  
**Total Files Modified:** 6 file  
**Migration Baru:** 1 (`create_alumni_requests_table`)  
**API Endpoint Baru:** 11 endpoint (3 import/export + 8 alumni-requests)  

---

## [2026-06-06] Phase 3A — RESMI DITUTUP — CRUD Alumni Dasar (Backend Full-Stack) — CLOSED

### Ringkasan Penutupan Resmi

Session 3A ditutup secara resmi pada 2026-06-06 setelah seluruh **backend task** diselesaikan dalam 4 batch commit. Semua 24 API route baru aktif, 2 Policy terdaftar di `AuthServiceProvider`, dan business rules kritis (one-current-job, auto-sync `is_employed`, double-guard ownership, guard delete tracer study) telah terverifikasi.

Frontend tasks (7 item) **di-carry-over ke Session 3B** karena backend selesai lebih cepat dari estimasi dan tidak menghalangi penutupan 3A. Carry-over ini tercatat sebagai keputusan teknis resmi (`3A→3B`) di `08_PHASE_TRACKER.md`.

**Verifikasi Penutupan:**
- [x] Semua backend task 3A berstatus `[x]` di Phase Tracker
- [x] 4 Batch commit berhasil push ke `main`
- [x] Konflik C-06 (namespace AlumniSelf) diselesaikan dan dicatat
- [x] 24 route API baru terdaftar (15 Admin + 9 AlumniSelf)
- [x] `AuthServiceProvider` diupdate dengan 2 policy baru
- [x] Session 3B status diubah menjadi `🔄 AKTIF`
- [x] Frontend carry-over 7 item dari 3A ke 3B dicatat di Phase Tracker

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
- `app/Http/Requests/Admin/StoreAlumniRequest.php`
- `app/Http/Requests/Admin/UpdateAlumniRequest.php`
- `app/Http/Requests/Admin/StoreAlumniEmploymentHistoryRequest.php`
- `app/Http/Requests/Admin/UpdateAlumniEmploymentHistoryRequest.php`

**Files Created (AlumniSelf Requests):**
- `app/Http/Requests/AlumniSelf/UpdateProfileRequest.php`
- `app/Http/Requests/AlumniSelf/UpdateEmploymentRequest.php`
- `app/Http/Requests/AlumniSelf/StoreEmploymentHistoryRequest.php`
- `app/Http/Requests/AlumniSelf/UpdateEmploymentHistoryRequest.php`

**Files Created (Resources):**
- `app/Http/Resources/AlumniResource.php`
- `app/Http/Resources/AlumniEmploymentHistoryResource.php`

**Files Created (Policies):**
- `app/Policies/AlumniPolicy.php`
- `app/Policies/AlumniEmploymentHistoryPolicy.php`

---

### Batch 4 — Controllers + AuthServiceProvider + Routes
**Commit:** 4 Controllers + AuthServiceProvider update + routes/api.php update

**Files Created (Admin Controllers):**
- `app/Http/Controllers/Api/Admin/AlumniController.php`
- `app/Http/Controllers/Api/Admin/AlumniEmploymentHistoryController.php`

**Files Created (AlumniSelf Controllers):**
- `app/Http/Controllers/Api/AlumniSelf/ProfileController.php`
- `app/Http/Controllers/Api/AlumniSelf/EmploymentHistoryController.php`

**Files Modified:**
- `app/Providers/AuthServiceProvider.php` — tambah 2 mapping policy
- `routes/api.php` — tambah 24 route baru (15 Admin + 9 AlumniSelf)

---

### Ringkasan Perubahan Phase 3A

**Total Files Created:** 16 file baru  
**Total Files Modified:** 2 file  
**Migration Baru:** 0 (diverifikasi dari Phase 1 skeleton)  
**API Endpoint Baru:** 24 endpoint  
**Konflik Diselesaikan:** C-06 (AlumniSelf namespace)  

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
- `GET  /api/v1/admin/audit-trails`
- `GET  /api/v1/admin/audit-trails/{id}`
- `GET  /api/v1/admin/activity-logs`
- `GET  /api/v1/admin/activity-logs/{id}`
- `DELETE /api/v1/admin/activity-logs` — purge
- `GET  /api/v1/admin/settings`
- `GET  /api/v1/admin/settings/{group}/{key}`
- `PUT  /api/v1/admin/settings/{group}/{key}`
- `PUT  /api/v1/admin/settings/batch`
- `GET  /api/v1/admin/institutions/{id}/detail`
- `PUT  /api/v1/admin/institutions/{id}/detail`

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
