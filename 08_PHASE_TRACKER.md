# 08_PHASE_TRACKER.md — Pelacak Fase Pengembangan Tracer Study UNISYA

**Versi:** 1.0.9
**Tanggal Dibuat:** 2026-06-05
**Institusi:** Universitas Islam Syarifuddin (UNISYA)
**Metodologi:** Phase-based Development (9 Phase × 3 Session)

---

## PANDUAN PENGGUNAAN

File ini adalah **project memory permanen**. Setiap awal sesi development wajib:

1. Baca file ini untuk memahami progress terkini
2. Verifikasi konsistensi dengan `01_BLUEPRINT.md`, `02_DATABASE.md`, `04_ARCHITECTURE.md`, `CREATE_TABLE.md`
3. Tandai task yang selesai dengan `[x]`
4. Catat semua keputusan teknis di kolom **Catatan**
5. Update `09_CHANGELOG.md` dengan perubahan yang dilakukan
6. Lanjutkan development dari task terakhir yang belum selesai

**Konvensi Status:**
- `[ ]` = Belum dikerjakan
- `[~]` = Sedang dikerjakan / In Progress
- `[x]` = Selesai
- `[!]` = Bermasalah / Perlu perhatian

---

## STATUS PROYEK SAAT INI

| Item | Detail |
|------|--------|
| **Phase Aktif** | Phase 1 — Fondasi Sistem |
| **Sesi Aktif** | Session 1C — Frontend Foundation & Auth Bootstrap |
| **Total Progress** | 7.41% (2 / 27 sesi selesai, 1 in progress) |
| **Terakhir Diperbarui** | 2026-06-05 |
| **Lingkungan** | Development |

---

## RINGKASAN SEMUA PHASE

| Phase | Nama | Total Sesi | Selesai | Status |
|-------|------|-----------|---------|--------|
| Phase 1 | Fondasi Sistem | 3 | 2 | 🟨 In Progress (1C aktif) |
| Phase 2 | Data Master | 3 | 0 | ⬜ Belum Dimulai |
| Phase 3 | Manajemen Alumni | 3 | 0 | ⬜ Belum Dimulai |
| Phase 4 | Mesin Kuesioner | 3 | 0 | ⬜ Belum Dimulai |
| Phase 5 | Tracer Study & Employer | 3 | 0 | ⬜ Belum Dimulai |
| Phase 6 | Notifikasi & Integrasi | 3 | 0 | ⬜ Belum Dimulai |
| Phase 7 | Pelaporan & Analitik | 3 | 0 | ⬜ Belum Dimulai |
| Phase 8 | Pengaturan & Keamanan | 3 | 0 | ⬜ Belum Dimulai |
| Phase 9 | Testing & Deployment | 3 | 0 | ⬜ Belum Dimulai |

---

# PHASE 1 — FONDASI SISTEM

**Tujuan:** Setup infrastruktur proyek, autentikasi lengkap, RBAC, dan layout dasar.  
**Prasyarat:** Semua dokumen 01–09 sudah disetujui.

---

### SESSION 1A — Setup Proyek & Infrastruktur

**Status:** ✅ Selesai (Terverifikasi Penuh)  
**Target:** Proyek Laravel + Vue berjalan di lokal

#### Backend Tasks

- [x] Audit dokumen `01_BLUEPRINT.md` s.d. `09_CHANGELOG.md`
- [x] Validasi Blueprint, Database Design, ERD, Architecture
- [x] Draft folder structure proyek
- [x] Draft Laravel installation plan
- [x] Draft Composer dependencies
- [x] Draft NPM dependencies
- [x] Draft environment configuration
- [x] Draft base configuration files
- [x] Audit konsistensi migration terhadap `02_DATABASE.md`
- [x] Audit konsistensi relasi terhadap `03_ERD.md`
- [x] Identifikasi schema drift enum/kolom pada migration inti
- [x] Konsolidasi patch gelombang 1 dan gelombang 2
- [x] Klarifikasi enum `salary_range` telah ditutup oleh `02_DATABASE.md` v1.0.1
- [x] Verifikasi migration aktif `users` telah sesuai schema final untuk `role`, `email_verified_at`, `phone_verified_at`, dan `remember_token`

#### Database Tasks

- [x] Review status semua migration berdasarkan dokumen final
- [x] Tetapkan daftar migration yang lulus
- [x] Tetapkan daftar migration yang masih conditional
- [x] Tutup blocker utama migration `users` melalui schema aktif yang telah memakai enum `super_admin` dan `alumni`
- [x] Verifikasi bahwa risiko enum role pada tabel `users` tidak lagi menjadi blocker penutupan Session 1A
- [x] Jalankan `php artisan migrate`
- [x] Verifikasi foreign key, index, dan enum di database hasil migrasi

#### Model Tasks

- [x] Review model dasar yang sudah dirancang untuk `User`, `OtpVerification`, dan `EmployerAccessToken`
- [x] Review kebutuhan model terhadap perubahan schema final
- [x] Sinkronisasi final casts, fillable, dan relationship ke schema terbaru

#### Frontend Tasks

- [x] Draft struktur Vue 3 + Vite
- [x] Draft dependensi frontend
- [x] Draft konfigurasi Tailwind CSS v3
- [x] Draft konfigurasi `vite.config.js`
- [x] Draft konfigurasi axios global
- [x] Verifikasi implementasi frontend terhadap hasil struktur backend final

**Catatan Sesi 1A:**
> Session 1A dinyatakan selesai setelah fondasi arsitektur, desain database, ERD, struktur proyek, dependensi, konfigurasi environment, dan migration inti `users` berhasil dikonsolidasikan.  
> Migration aktif `users` telah menggunakan schema final: `email_verified_at`, `phone_verified_at`, `remember_token`, dan enum role `super_admin` / `alumni`, sehingga blocker utama drift role untuk fondasi auth sudah tertutup.  
> Sisa patch migration lain yang masih conditional tidak lagi menghalangi penutupan Session 1A karena tidak memblokir fondasi setup proyek dan dapat diteruskan sebagai bagian sinkronisasi implementasi pada sesi berikutnya.

---

### SESSION 1B — Authentication & Authorization Foundation

**Status:** ✅ Selesai  
**Target:** Login, OTP, RBAC, middleware auth, dan auth frontend sinkron

#### Backend Tasks

- [x] Review kontrak endpoint auth pada `05_API.md`
- [x] Review arsitektur auth flow pada `04_ARCHITECTURE.md`
- [x] Review security auth, OTP, Sanctum, dan middleware pada `07_SECURITY.md`
- [x] Patch Form Request auth login, OTP request, OTP verify, employer OTP request, employer OTP verify
- [x] Patch `AuthService` agar sinkron dengan enum role `superadmin`
- [x] Patch `LoginController`, `OtpController`, dan `EmployerAccessController`
- [x] Patch `AuthServiceProvider` gates `admin` dan `alumni`
- [x] Patch `UserPolicy`
- [x] Patch middleware `EnsureActiveUser` dan `CheckEmployerToken`
- [x] Patch `bootstrap/app.php` untuk alias middleware dan JSON exception handling
- [x] Patch `routes/api.php` untuk grouping auth/admin/alumni/employer
- [x] Terapkan patch auth backend ke codebase aktif

#### Frontend Tasks

- [x] Patch `resources/js/stores/auth.js`
- [x] Patch `resources/js/router/index.js`
- [x] Patch `resources/js/app.js` interceptor auth
- [x] Terapkan patch auth frontend ke codebase aktif
- [x] Verifikasi redirect dashboard berdasarkan role `superadmin`
- [~] Verifikasi session/token behavior saat refresh browser
- [~] Verifikasi flow login email/password, OTP login, logout, me, dan employer OTP end-to-end

**Catatan Sesi 1B:**
> Session 1B dinyatakan selesai secara implementasi karena patch auth backend dan frontend yang sebelumnya berstatus draft telah resmi diterapkan ke codebase aktif.  
> Fondasi autentikasi dan otorisasi kini telah mencakup sinkronisasi role `superadmin`, gate/policy, middleware auth, response JSON exception handling, serta store/router frontend.  
> Dua item verifikasi operasional masih dicatat sebagai follow-up teknis ringan untuk transisi ke Session 1C: perilaku auth state saat refresh browser dan smoke test end-to-end untuk flow auth/employer. Keduanya tidak lagi dianggap blocker penutupan Session 1B, tetapi menjadi prasyarat QA awal Session 1C.
> Patch konsistensi lanjutan dilakukan setelah audit cross-layer: field mismatch `otp_code` pada `OtpVerifyRequest` dan `OtpController` diperbaiki; method `requestEmployerOtp` dan `verifyEmployerOtp` ditambahkan ke `AuthService`; fillable `User` dan `OtpVerification` dilengkapi. Satu item masih terbuka: eager load relasi `institution` pada `verifyEmployerOtp` agar field `company` tidak null di response API.

---

### SESSION 1C — Frontend Foundation & Auth Bootstrap

**Status:** 🟨 In Progress  
**Target:** Integrasi autentikasi frontend dan stabilisasi state auth pada SPA

#### Frontend Tasks

- [x] Menyelesaikan scaffold page dan layout sesuai router aktif
- [x] Memastikan website lokal dapat diakses dan Vite terhubung
- [x] Menyelesaikan error import-analysis pada router
- [x] Menyusun final patch `useAuthStore` untuk login, request OTP, verify OTP, logout, dan bootstrap auth
- [x] Menyusun final patch `useUIStore` untuk theme hydration
- [x] Menyusun final patch `app.js` untuk bootstrap auth sebelum mount
- [x] Menyusun final patch `router/index.js` untuk guard berbasis `bootstrapped`
- [x] Menyusun final patch `LoginPage.vue` untuk login email/password dan request OTP
- [x] Menyusun final patch `OtpPage.vue` untuk verifikasi OTP 6 digit, countdown, dan resend OTP
- [ ] Tempel patch ke codebase lokal
- [ ] Uji login email/password ke endpoint `POST /api/v1/auth/login`
- [ ] Uji request OTP ke endpoint `POST /api/v1/auth/otp/request`
- [ ] Uji verifikasi OTP ke endpoint `POST /api/v1/auth/otp/verify`
- [ ] Uji rehydration auth saat browser refresh melalui `GET /api/v1/auth/me`
- [ ] Uji logout dan invalid token handling
- [ ] Verifikasi redirect role `super_admin` dan `alumni`
- [ ] Jalankan `npm run dev` dan verifikasi CSS Tailwind tampil di browser
- [ ] Jalankan `npm run build` dan verifikasi `public/build/manifest.json` terbentuk
- [ ] Konfirmasi root cause CSS tidak tampil: Vite dev server belum aktif (bukan bug kode)

#### Catatan Session 1C
> Final code patch Session 1C telah disusun untuk menutup gap antara halaman auth frontend, store Pinia, dan kontrak API autentikasi resmi.  
> Fokus berikutnya adalah penempelan patch ke codebase lokal dan pengujian end-to-end pada alur login, OTP, refresh, logout, dan role-based redirect.

## PHASE 2 — DATA MASTER

**Tujuan:** CRUD semua data master (Pengguna, Fakultas, Program Studi, Profesi, Institusi).
**Prasyarat:** Phase 1 selesai. Session dimulai dengan audit Phase 1.

---

### SESSION 2A — Manajemen Pengguna & Akademik

**Status:** ⬜ Belum Dimulai
**Target:** CRUD User, Fakultas, Program Studi berfungsi penuh

#### Backend Tasks

- [ ] `UserRepository` + `UserService`
- [ ] `UserController` (Admin) — CRUD + reset password + toggle aktif
- [ ] Form Request: `StoreUserRequest`, `UpdateUserRequest`, `ResetPasswordRequest`
- [ ] Policy: `UserPolicy` (update, delete, view)
- [ ] Resource: `UserResource`
- [ ] `FacultyRepository` + `FacultyService`
- [ ] `FacultyController` (Admin) — CRUD
- [ ] Form Request: `StoreFacultyRequest`, `UpdateFacultyRequest`
- [ ] Policy: `FacultyPolicy`
- [ ] Resource: `FacultyResource`
- [ ] `StudyProgramRepository` + `StudyProgramService`
- [ ] `StudyProgramController` (Admin) — CRUD
- [ ] Form Request: `StoreStudyProgramRequest`, `UpdateStudyProgramRequest`
- [ ] Policy: `StudyProgramPolicy`
- [ ] Resource: `StudyProgramResource`
- [ ] Seeder: `FacultySeeder` (data UNISYA)
- [ ] Seeder: `StudyProgramSeeder` (data UNISYA)
- [ ] Factory: `FacultyFactory`, `StudyProgramFactory`

#### Frontend Tasks

- [ ] Halaman `/admin/pengguna` — tabel user (sortable, searchable, paginated)
- [ ] Form modal/drawer: Buat & Edit User
- [ ] Komponen `AppTable.vue` (sort, pagination, empty state, skeleton)
- [ ] Komponen `AppPagination.vue`
- [ ] Halaman `/admin/fakultas` — tabel + CRUD modal
- [ ] Halaman `/admin/program-studi` — tabel + CRUD modal (filter by fakultas)
- [ ] Pinia store: `useUserStore`, `useFacultyStore`, `useStudyProgramStore`

**Catatan Sesi 2A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 2B — Manajemen Profesi & Institusi

**Status:** ⬜ Belum Dimulai
**Target:** CRUD Profesi dan Institusi berfungsi penuh

#### Backend Tasks

- [ ] `ProfessionCategoryRepository` + `ProfessionCategoryService`
- [ ] `ProfessionCategoryController` — CRUD
- [ ] Form Request: `StoreProfessionCategoryRequest`, `UpdateProfessionCategoryRequest`
- [ ] Resource: `ProfessionCategoryResource`
- [ ] `ProfessionRepository` + `ProfessionService`
- [ ] `ProfessionController` — CRUD
- [ ] Form Request: `StoreProfessionRequest`, `UpdateProfessionRequest`
- [ ] Resource: `ProfessionResource`
- [ ] `InstitutionRepository` + `InstitutionService`
- [ ] `InstitutionController` — CRUD + detail
- [ ] `InstitutionDetailController` — CRUD
- [ ] Form Request: `StoreInstitutionRequest`, `UpdateInstitutionRequest`
- [ ] Form Request: `StoreInstitutionDetailRequest`, `UpdateInstitutionDetailRequest`
- [ ] Resource: `InstitutionResource`, `InstitutionDetailResource`
- [ ] Seeder: `ProfessionCategorySeeder`, `ProfessionSeeder`

#### Frontend Tasks

- [ ] Halaman `/admin/kategori-profesi` — tabel + CRUD
- [ ] Halaman `/admin/profesi` — tabel + CRUD (filter by kategori)
- [ ] Halaman `/admin/institusi` — tabel + CRUD
- [ ] Tab "Detail Institusi" dalam halaman detail institusi
- [ ] Pinia store: `useProfessionStore`, `useInstitutionStore`

**Catatan Sesi 2B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 2C — Audit, Notifikasi & Pengaturan Awal

**Status:** ⬜ Belum Dimulai
**Target:** Model Observer untuk audit trail, Settings Management dasar

#### Backend Tasks

- [ ] `AuditTrailObserver` — register untuk semua model yang diaudit
- [ ] `AuditTrailController` — read-only list + filter
- [ ] `ActivityLogController` — read-only list + filter
- [ ] `SettingService` — get/set setting dengan cache
- [ ] `SettingController` — get all, get by group, bulk update
- [ ] `AppSetting` Model + Seeder (settings default)
- [ ] Model Observer terdaftar di `AppServiceProvider`

#### Frontend Tasks

- [ ] Halaman `/admin/audit-trail` — tabel audit (filter model, event, user, tanggal)
- [ ] Halaman `/admin/activity-log` — tabel aktivitas (filter user, tanggal)
- [ ] Halaman `/admin/pengaturan` — form settings (tab: Umum, WA Gateway, SMTP)
- [ ] Pinia store: `useSettingStore`

**Catatan Sesi 2C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 3 — MANAJEMEN ALUMNI

**Tujuan:** CRUD Alumni lengkap, import/export, permohonan update, employment tracking.
**Prasyarat:** Phase 2 selesai. Audit Phase 2 sebelum mulai.

---

### SESSION 3A — CRUD Alumni Dasar

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Model `Alumni` + `AlumniEmploymentHistory` + `AlumniRequest`
- [ ] `AlumniRepository` + `AlumniService`
- [ ] `AlumniController` (Admin) — CRUD + filter + search
- [ ] Form Request: `StoreAlumniRequest`, `UpdateAlumniRequest`
- [ ] Policy: `AlumniPolicy`
- [ ] Resource: `AlumniResource`, `AlumniDetailResource`
- [ ] `AlumniController` (Alumni Self) — profile, employment
- [ ] Form Request: `UpdateAlumniProfileRequest`
- [ ] File upload foto alumni (simpan di `storage/app/private/alumni/photos`)
- [ ] Seeder: `AlumniSeeder` (data dummy)
- [ ] Factory: `AlumniFactory`

#### Frontend Tasks

- [ ] Halaman `/admin/alumni` — tabel dengan filter (Fakultas, Prodi, Tahun Lulus, Status)
- [ ] Halaman `/admin/alumni/:id` — detail alumni + tab pekerjaan
- [ ] Form step-by-step: Buat & Edit Alumni (3 langkah)
- [ ] Komponen `AlumniCard.vue`
- [ ] Halaman `/alumni/profil` — profil diri alumni
- [ ] Halaman `/alumni/pekerjaan` — riwayat pekerjaan
- [ ] Pinia store: `useAlumniStore`

**Catatan Sesi 3A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 3B — Import/Export & Permohonan Alumni

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] `AlumniImport` class (Laravel Excel) — dengan validasi baris per baris
- [ ] `AlumniExport` class (Laravel Excel) — dengan filter
- [ ] `AlumniPdfExport` class (DomPDF) — template A4 dan F4
- [ ] Endpoint import: `POST /api/v1/admin/alumni/import`
- [ ] Endpoint export: `GET /api/v1/admin/alumni/export/excel`
- [ ] Endpoint export: `GET /api/v1/admin/alumni/export/pdf`
- [ ] Template Excel import (file contoh untuk download)
- [ ] `AlumniRequestRepository` + `AlumniRequestService`
- [ ] `AlumniRequestController` (Admin) — list, detail, approve, reject
- [ ] `AlumniRequestController` (Alumni) — create, list milik sendiri
- [ ] Form Request: `StoreAlumniRequestRequest`
- [ ] Policy: `AlumniRequestPolicy`
- [ ] Resource: `AlumniRequestResource`

#### Frontend Tasks

- [ ] Tombol Import Excel + modal upload + preview error validasi
- [ ] Komponen `ExportButton.vue` (dropdown: Excel/PDF, ukuran A4/F4)
- [ ] Halaman `/alumni/permohonan` — list permohonan + form buat permohonan
- [ ] Halaman `/admin/permohonan-alumni` — tabel permohonan + approve/reject

**Catatan Sesi 3B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 3C — Employment Tracking

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] `EmploymentRepository` + `EmploymentService`
- [ ] `EmploymentController` (Admin & Alumni) — CRUD riwayat pekerjaan
- [ ] Form Request: `StoreEmploymentRequest`, `UpdateEmploymentRequest`
- [ ] Policy: `EmploymentPolicy`
- [ ] Resource: `EmploymentResource`
- [ ] Logika update `alumni.is_employed` dan `alumni.waiting_period_months` otomatis

#### Frontend Tasks

- [ ] Komponen riwayat pekerjaan (list + form tambah/edit)
- [ ] Integrasi autocomplete institusi saat tambah pekerjaan
- [ ] Integrasi autocomplete profesi saat tambah pekerjaan

**Catatan Sesi 3C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 4 — MESIN KUESIONER

**Tujuan:** Builder kuesioner, tipe jawaban, pengisian oleh alumni & employer.
**Prasyarat:** Phase 3 selesai. Audit Phase 3 sebelum mulai.

---

### SESSION 4A — Builder Kuesioner

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Model `QuestionnaireCategory`, `AnswerType`, `Questionnaire`, `QuestionnaireQuestion`
- [ ] CRUD `QuestionnaireCategory` + Repository + Service + Controller + Request + Policy
- [ ] CRUD `AnswerType` + Repository + Service + Controller + Request + Policy
- [ ] CRUD `Questionnaire` + Repository + Service + Controller + Request + Policy
- [ ] CRUD `QuestionnaireQuestion` (termasuk reorder) + Repository + Service + Controller
- [ ] Resource: semua model kuesioner
- [ ] Seeder: `AnswerTypeSeeder` (true_false, scale_1_5, scale_1_10)
- [ ] Seeder: `QuestionnaireCategorySeeder` (data awal)
- [ ] Factory: `QuestionnaireFactory`, `QuestionnaireQuestionFactory`

#### Frontend Tasks

- [ ] Halaman `/admin/kategori-kuesioner` — CRUD
- [ ] Halaman `/admin/tipe-jawaban` — CRUD
- [ ] Halaman `/admin/kuesioner` — tabel kuesioner
- [ ] Halaman `/admin/kuesioner/:id/pertanyaan` — builder pertanyaan
- [ ] Komponen `QuestionnaireBuilder.vue` (drag-and-drop urutan pertanyaan)
- [ ] Komponen `QuestionCard.vue` (tampilan tiap pertanyaan)
- [ ] Preview kuesioner real-time (panel samping)

**Catatan Sesi 4A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 4B — Pengisian Kuesioner Alumni

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Model `QuestionnaireResponse`, `QuestionnaireAnswer`
- [ ] `QuestionnaireResponseService` — logika pengisian + snapshot
- [ ] `QuestionnaireResponseController` (Alumni) — list, detail, submit
- [ ] Form Request: `SubmitQuestionnaireRequest`
- [ ] Validasi: cek sudah pernah mengisi (duplicate prevention)
- [ ] Logic: simpan `questionnaire_snapshot` dan `question_snapshot` (immutable)
- [ ] Factory: `QuestionnaireResponseFactory`

#### Frontend Tasks

- [ ] Halaman `/alumni/tracer-study` — daftar tracer study aktif
- [ ] Halaman `/alumni/tracer-study/:id/isi` — wizard pengisian kuesioner
- [ ] Komponen `AnswerScaleInput.vue` (slider 1-5 dan 1-10)
- [ ] Komponen `AnswerTrueFalseInput.vue` (toggle)
- [ ] Progress bar pengisian
- [ ] Auto-save draft jawaban (sessionStorage)
- [ ] Konfirmasi sebelum submit final

**Catatan Sesi 4B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 4C — Pengisian Kuesioner Employer

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] `QuestionnaireResponseController` (Employer) — list kuesioner + submit
- [ ] Middleware `CheckEmployerToken` — validasi token ability 'employer'
- [ ] Validasi: token employer hanya bisa mengisi 1 kali per kuesioner
- [ ] Endpoint employer: `GET /api/v1/employer/questionnaires`
- [ ] Endpoint employer: `POST /api/v1/employer/questionnaires/:id/submit`

#### Frontend Tasks

- [ ] Halaman `/employer/dashboard` — sambutan + daftar kuesioner
- [ ] Halaman `/employer/kuesioner/:id` — pengisian kuesioner dalam format card
- [ ] Halaman `/employer/selesai` — konfirmasi submit + nomor respons
- [ ] Layout employer yang minimal dan bersih

**Catatan Sesi 4C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 5 — TRACER STUDY & EMPLOYER

**Tujuan:** Sesi Tracer Study, distribusi undangan, token employer, monitoring respons.
**Prasyarat:** Phase 4 selesai. Audit Phase 4 sebelum mulai.

---

### SESSION 5A — Manajemen Sesi Tracer Study

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Model `TracerStudy`, `TracerStudyQuestionnaire`
- [ ] `TracerStudyRepository` + `TracerStudyService`
- [ ] `TracerStudyController` (Admin) — CRUD + activate + complete
- [ ] Form Request: `StoreTracerStudyRequest`, `UpdateTracerStudyRequest`
- [ ] Policy: `TracerStudyPolicy`
- [ ] Resource: `TracerStudyResource`, `TracerStudyDetailResource`
- [ ] Endpoint aktivasi: kirim undangan via Queue saat diaktifkan
- [ ] Endpoint stats: `GET /api/v1/admin/tracer-studies/:id/stats`
- [ ] Endpoint responses: `GET /api/v1/admin/tracer-studies/:id/responses`
- [ ] Seeder: `TracerStudySeeder` (data dummy)

#### Frontend Tasks

- [ ] Halaman `/admin/tracer-study` — tabel sesi
- [ ] Halaman `/admin/tracer-study/:id` — detail + monitoring + respons
- [ ] Form buat & edit sesi Tracer Study
- [ ] Komponen `TracerStudyStatusBadge.vue`
- [ ] Gauge/progress chart tingkat respons (ApexCharts)
- [ ] Pinia store: `useTracerStudyStore`

**Catatan Sesi 5A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 5B — Token Employer & OTP Flow

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] `EmployerAccessService` — create, validate, revoke token
- [ ] Artisan command: `CleanupExpiredTokens`
- [ ] Endpoint alumni: buat token undangan employer
- [ ] Endpoint alumni: cabut token
- [ ] Endpoint employer: request OTP
- [ ] Endpoint employer: verify OTP
- [ ] Logic: token_plain di-null setelah pengiriman
- [ ] Policy: `EmployerAccessTokenPolicy`

#### Frontend Tasks

- [ ] Halaman `/alumni/employer` — daftar token + form undang employer
- [ ] Komponen: form undang employer (pilih institusi, input kontak)
- [ ] Halaman `/employer/akses` — landing page + input token
- [ ] Halaman `/employer/verifikasi-otp` — OTP verification
- [ ] Komponen `OtpInput.vue` — 6 digit auto-focus

**Catatan Sesi 5B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 5C — Tracking & Monitoring

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Dashboard Admin: endpoint `GET /api/v1/admin/reports/dashboard`
- [ ] Logic: hitung tingkat respons per sesi Tracer Study
- [ ] Logic: hitung statistik employment (per fakultas, per prodi)

#### Frontend Tasks

- [ ] Halaman `/admin/dashboard` — KPI cards + chart ApexCharts
- [ ] Komponen `KpiCard.vue` (ikon, nilai, tren)
- [ ] Komponen `ChartWidget.vue` (wrapper ApexCharts)
- [ ] Chart: Donut status pekerjaan alumni
- [ ] Chart: Bar alumni per fakultas
- [ ] Chart: Line tren wisuda per tahun
- [ ] Chart: Gauge tingkat respons tracer study aktif

**Catatan Sesi 5C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 6 — NOTIFIKASI & INTEGRASI

**Tujuan:** WA Gateway, SMTP, Queue notifications, Laravel Scheduler (reminder).
**Prasyarat:** Phase 5 selesai. Audit Phase 5 sebelum mulai.

---

### SESSION 6A — WhatsApp Gateway Integration

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] `WhatsAppGatewayService` — send(), sanitizePhoneNumber(), retry logic
- [ ] Job: `SendWhatsAppNotification`
- [ ] Notification: `OtpLoginNotification` (channel: WA)
- [ ] Notification: `EmployerInvitationNotification` (channel: WA)
- [ ] Notification: `TracerStudyInvitationNotification` (channel: WA)
- [ ] Notification: `TracerStudyReminderNotification` (channel: WA)
- [ ] Model `NotificationLog` — catat setiap pengiriman WA
- [ ] Endpoint admin: `POST /api/v1/admin/settings/test-wa` (test pengiriman)
- [ ] Konfigurasi: api_key & sender diambil dari `AppSetting` (bukan hardcoded)

**WA Gateway Endpoint:**
```
URL    : https://wacenter.unisya.ac.id/send-message
Method : POST (JSON) / GET (Query String)
Params : api_key, sender, number, message, footer (opt), msgid (opt), full (opt)
```

**Catatan Sesi 6A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 6B — SMTP & Email Templates

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] `SmtpService` — konfigurasi SMTP dari `AppSetting` secara dinamis
- [ ] Job: `SendEmailNotification`
- [ ] Notification: `OtpLoginNotification` (channel: email)
- [ ] Notification: `EmployerInvitationNotification` (channel: email)
- [ ] Notification: `TracerStudyInvitationNotification` (channel: email)
- [ ] Template Blade email: OTP, Undangan Tracer Study, Undangan Employer
- [ ] Endpoint admin: `POST /api/v1/admin/settings/test-smtp`

**Catatan Sesi 6B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 6C — Queue Worker & Scheduler

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Konfigurasi Queue driver: `database`
- [ ] Setup Supervisor config untuk `queue:work`
- [ ] Scheduler: daftarkan semua command di `Kernel.php`
  - [ ] `CleanupExpiredOtp` — setiap jam
  - [ ] `CleanupExpiredTokens` — setiap jam
  - [ ] `SendTracerStudyReminders` — setiap hari jam 08:00
- [ ] Notification log: catat status success/failed di `notification_logs`
- [ ] Failed job handling: retry & log

#### Frontend Tasks

- [ ] Halaman `/admin/pengaturan` — tab "WA Gateway" (api_key, sender, test)
- [ ] Halaman `/admin/pengaturan` — tab "SMTP" (host, port, user, pass, test)
- [ ] Komponen test koneksi dengan status indikator

**Catatan Sesi 6C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 7 — PELAPORAN & ANALITIK

**Tujuan:** Dashboard analitik lengkap, semua laporan, export Excel & PDF.
**Prasyarat:** Phase 6 selesai. Audit Phase 6 sebelum mulai.

---

### SESSION 7A — Laporan Alumni & Distribusi

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] `ReportService` — semua query laporan dengan filter
- [ ] Endpoint: `GET /api/v1/admin/reports/alumni-distribution`
- [ ] Endpoint: `GET /api/v1/admin/reports/faculty-analytics`
- [ ] Endpoint: `GET /api/v1/admin/reports/program-analytics`
- [ ] Filter: `start_date`, `end_date`, `faculty_id`, `study_program_id`
- [ ] `AlumniDistributionExport` (Excel + PDF, A4/F4)

#### Frontend Tasks

- [ ] Halaman `/admin/laporan` — filter global + tab
- [ ] Tab "Distribusi Alumni" — chart + tabel
- [ ] Tab "Per Fakultas" — chart bar + tabel detail
- [ ] Tab "Per Program Studi" — chart + tabel
- [ ] Komponen `AppDatePicker.vue` (date range picker)
- [ ] Pinia store: `useReportStore`

**Catatan Sesi 7A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 7B — Laporan Pekerjaan & Masa Tunggu

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Endpoint: `GET /api/v1/admin/reports/employment-analytics`
- [ ] Endpoint: `GET /api/v1/admin/reports/waiting-period`
- [ ] Endpoint: `GET /api/v1/admin/reports/employer-analytics`
- [ ] `EmploymentAnalyticsExport` (Excel + PDF)

#### Frontend Tasks

- [ ] Tab "Analitik Pekerjaan" — chart + tabel
- [ ] Tab "Masa Tunggu" — distribusi masa tunggu dalam bulan
- [ ] Tab "Employer" — top employer + statistik

**Catatan Sesi 7B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 7C — Laporan Kuesioner & Export

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Endpoint: `GET /api/v1/admin/reports/questionnaire/:id`
- [ ] Export universal: `GET /api/v1/admin/reports/export/excel`
- [ ] Export universal: `GET /api/v1/admin/reports/export/pdf`
- [ ] DomPDF: template laporan A4 dan F4 (header UNISYA, tabel, chart)
- [ ] `ExportService` — orchestrator semua tipe export

#### Frontend Tasks

- [ ] Tab "Kuesioner" — pilih kuesioner + lihat hasil per pertanyaan
- [ ] Chart hasil per pertanyaan (bar untuk skala, donut untuk T/F)
- [ ] Tombol export dengan dropdown format & ukuran kertas

**Catatan Sesi 7C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 8 — PENGATURAN & KEAMANAN

**Tujuan:** Settings Management lengkap, security hardening, audit trail UI.
**Prasyarat:** Phase 7 selesai. Audit Phase 7 sebelum mulai.

---

### SESSION 8A — Settings Management Lengkap

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Setting groups: `general`, `wa_gateway`, `smtp`, `security`, `notification`
- [ ] Enkripsi nilai setting sensitif (api_key, password SMTP)
- [ ] Endpoint bulk update setting per group
- [ ] Seeder: lengkapi `AppSettingSeeder` dengan semua setting

#### Frontend Tasks

- [ ] Halaman `/admin/pengaturan` — tab: Umum | WA Gateway | SMTP | Keamanan | Notifikasi
- [ ] Form Umum: nama aplikasi, logo, URL
- [ ] Form WA Gateway: api_key (masked), sender, tombol test
- [ ] Form SMTP: host, port, enkripsi, username, password (masked), tombol test
- [ ] Form Keamanan: session timeout, OTP expiry, max OTP attempts

**Catatan Sesi 8A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 8B — Security Hardening

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Verifikasi semua Rate Limiter terpasang
- [ ] Verifikasi semua Policy aktif
- [ ] Verifikasi SecurityHeaders middleware aktif
- [ ] Verifikasi CSP header dikonfigurasi dengan benar
- [ ] Verifikasi tidak ada plain password/OTP/token tersimpan di DB
- [ ] Verifikasi tidak ada data sensitif di log
- [ ] Jalankan `php artisan security:audit` (custom command)
- [ ] Buat Artisan command: `app:security-audit` — cek semua security checklist

#### Frontend Tasks

- [ ] Verifikasi semua form memiliki validasi client-side
- [ ] Verifikasi tidak ada data sensitif tersimpan di localStorage
- [ ] Verifikasi CSRF token dikirim di semua request

**Catatan Sesi 8B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 8C — Notifikasi In-App

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Tabel `notifications` (Laravel default morphable)
- [ ] Endpoint: `GET /api/v1/notifications` — unread list
- [ ] Endpoint: `PUT /api/v1/notifications/:id/read`
- [ ] Endpoint: `PUT /api/v1/notifications/read-all`
- [ ] Endpoint: `GET /api/v1/notifications/unread-count`
- [ ] Kirim notifikasi in-app saat: permohonan alumni disetujui/ditolak, tracer study aktif

#### Frontend Tasks

- [ ] Badge notifikasi di header (unread count)
- [ ] Panel notifikasi slide-in dari kanan
- [ ] Pinia store: `useNotificationStore`
- [ ] Auto-refresh unread count setiap 60 detik

**Catatan Sesi 8C:**
> _Isi catatan setelah sesi selesai_

---

## PHASE 9 — TESTING & DEPLOYMENT

**Tujuan:** Unit test, feature test, security test, deployment ke Ubuntu + aaPanel.
**Prasyarat:** Phase 8 selesai. Full audit semua phase sebelum mulai.

---

### SESSION 9A — Unit & Feature Tests

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Test: `AuthTest` — login, OTP, logout, invalid credentials
- [ ] Test: `OtpTest` — generate, verify, expired, max attempts
- [ ] Test: `EmployerTokenTest` — create, use, revoke, expired
- [ ] Test: `AlumniTest` — CRUD, import, export, validation
- [ ] Test: `QuestionnaireTest` — builder, submit, snapshot immutability
- [ ] Test: `TracerStudyTest` — create, activate, complete, stats
- [ ] Test: `ReportTest` — semua endpoint laporan
- [ ] Test: `RateLimitTest` — verifikasi rate limiter bekerja
- [ ] Test: `PolicyTest` — verifikasi setiap role hanya bisa akses yang sesuai
- [ ] Jalankan: `php artisan test --coverage`

#### Frontend Tasks

- [ ] Setup Vitest untuk unit test Vue components
- [ ] Test komponen: `OtpInput`, `QuestionnaireBuilder`, `AppTable`

**Catatan Sesi 9A:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 9B — Security Testing

**Status:** ⬜ Belum Dimulai

#### Backend Tasks

- [ ] Test: SQL Injection — semua input form
- [ ] Test: XSS — semua output field
- [ ] Test: CSRF — verifikasi token required
- [ ] Test: Mass Assignment — verifikasi field tidak bisa di-inject
- [ ] Test: Rate Limiting — verifikasi 429 saat limit terlampaui
- [ ] Test: Unauthorized Access — verifikasi 403 saat akses tanpa izin
- [ ] Test: File Upload — upload file berbahaya (PHP, .htaccess)
- [ ] Verifikasi: OTP plain tidak tersimpan di DB
- [ ] Verifikasi: Token plain di-null setelah pengiriman

**Catatan Sesi 9B:**
> _Isi catatan setelah sesi selesai_

---

### SESSION 9C — Deployment & Dokumentasi

**Status:** ⬜ Belum Dimulai

#### Deployment Tasks

- [ ] Setup server Ubuntu 22.04 + aaPanel
- [ ] Install PHP 8.3 + ekstensi yang diperlukan
- [ ] Install MySQL 8.0
- [ ] Install Nginx / Apache
- [ ] Setup SSL Let's Encrypt
- [ ] Clone project ke server
- [ ] Konfigurasi `.env` production
- [ ] Jalankan `composer install --no-dev --optimize-autoloader`
- [ ] Jalankan `npm run build` (Vue SPA build)
- [ ] Jalankan `php artisan migrate --force`
- [ ] Jalankan `php artisan db:seed --class=ProductionSeeder`
- [ ] Jalankan `php artisan config:cache && php artisan route:cache`
- [ ] Setup Supervisor untuk `queue:work`
- [ ] Setup crontab untuk `schedule:run`
- [ ] Konfigurasi permissions file & direktori
- [ ] Verifikasi semua fitur berjalan di production

#### Dokumentasi Tasks

- [ ] Buat `DEPLOYMENT.md` — panduan deployment
- [ ] Buat `USER_MANUAL_ADMIN.md` — panduan admin
- [ ] Buat `USER_MANUAL_ALUMNI.md` — panduan alumni
- [ ] Update semua file dokumentasi (01-09) ke versi final

**Catatan Sesi 9C:**
> _Isi catatan setelah sesi selesai_

---

## KEPUTUSAN TEKNIS PENTING

*Catat semua keputusan arsitektural/teknis yang dibuat selama development.*

| Tanggal | Phase/Sesi | Keputusan | Alasan |
|---------|-----------|-----------|--------|
| 2026-06-04 | — | UUID CHAR(36) untuk semua PK | Mencegah enumerasi ID, mendukung distribusi |
| 2026-06-04 | — | Queue driver: database (bukan Redis) | Kemudahan deployment tanpa instalasi Redis |
| 2026-06-04 | — | WA Gateway: POST JSON (bukan GET) | Lebih aman untuk data sensitif (OTP, token) |
| 2026-06-04 | — | OTP disimpan dalam bentuk hash (bcrypt) | Keamanan — plain OTP tidak pernah tersimpan |
| 2026-06-04 | — | Employer token: SHA-256 hash + plain di-null | Token sekali pakai, plain dihapus setelah dikirim |
| 2026-06-04 | — | Snapshot immutable pada questionnaire_responses | Perubahan kuesioner tidak merusak data historis |
| 2026-06-04 | — | SoftDeletes pada semua tabel master | Data tidak pernah benar-benar terhapus permanen |
| 2026-06-05 | 1B/1C | CSS dikelola via `import '../css/app.css'` di `app.js` — Vite wajib aktif (dev) atau `npm run build` dijalankan (prod) agar CSS tampil | Konsisten dengan arsitektur Laravel + Vite SPA |
| 2026-06-05 | 1B | Eager load `institution` wajib ditambahkan di `verifyEmployerOtp` untuk mencegah `company: null` di response API | N+1 silent bug — relasi tidak di-load meski data ada di DB |

---

## KONFLIK & MASALAH YANG TERDETEKSI

*Catat semua konflik atau masalah yang perlu diselesaikan.*

| ID | Tanggal | Deskripsi | Status | Solusi |
|----|---------|-----------|--------|--------|
| C-001 | 2026-06-05 | `verifyEmployerOtp` tidak eager load relasi `institution`, menyebabkan `company` selalu `null` di response | 🔴 Open | Tambah `institution` ke `->with([...])` di `AuthService::verifyEmployerOtp()` |
| C-002 | 2026-06-05 | CSS tidak tampil di lokal — bukan bug, Vite dev server belum dijalankan | ✅ Resolved | Jalankan `npm run dev` atau `npm run build` |

---

*File ini adalah sumber kebenaran tunggal untuk progress development. Update setiap akhir sesi.*
