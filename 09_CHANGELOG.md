# 09_CHANGELOG.md — Riwayat Perubahan Tracer Study UNISYA

**Versi:** 1.0.8
**Tanggal Dibuat:** 2026-06-05
**Institusi:** Universitas Islam Syarifuddin (UNISYA)
**Format:** [Semantic Versioning](https://semver.org/) — MAJOR.MINOR.PATCH

---

## PANDUAN CHANGELOG

File ini mencatat **semua perubahan** yang terjadi pada proyek, termasuk:

- Perubahan dokumentasi (file 01–09)
- Penambahan fitur baru
- Perubahan desain/arsitektur
- Perbaikan bug
- Pembaruan keputusan teknis
- Perubahan breaking (yang tidak backward-compatible)

### Konvensi Entry

Setiap entry menggunakan format berikut:

```
### [VERSI] — TANGGAL

#### Added (Ditambahkan)
- Fitur atau dokumen baru

#### Changed (Diubah)
- Perubahan pada fitur yang sudah ada

#### Fixed (Diperbaiki)
- Perbaikan bug

#### Removed (Dihapus)
- Fitur atau kode yang dihapus

#### Security (Keamanan)
- Perubahan terkait keamanan

#### Breaking (Perubahan Tidak Kompatibel)
- Perubahan yang memerlukan update kode/konfigurasi lain
```

### Aturan Versi

| Tipe Perubahan | Increment |
|----------------|-----------|
| Perubahan breaking / arsitektur besar | MAJOR (x.0.0) |
| Fitur baru yang backward-compatible | MINOR (0.x.0) |
| Bug fix, perbaikan kecil, update dokumentasi | PATCH (0.0.x) |

---

## [UNRELEASED] — Upcoming Changes

*Catat perubahan yang sedang direncanakan atau sedang dalam development di sini sebelum dirilis.*

---

## [1.0.0] — 2026-06-04

### Added (Ditambahkan)

#### Dokumentasi Awal (Knowledge Base)

- **01_BLUEPRINT.md** `v1.0.0` — Dokumen blueprint sistem lengkap:
  - Ringkasan sistem dan latar belakang
  - Spesifikasi kebutuhan fungsional (10 modul, 60+ requirement)
  - Spesifikasi kebutuhan non-fungsional (performa, keamanan, skalabilitas)
  - Matriks peran pengguna (Super Admin, Alumni, Pengguna Alumni)
  - Alur sistem utama (login, employer access, tracer study)
  - Modul breakdown (24 modul)
  - Roadmap pengembangan (9 phase × 3 session)

- **02_DATABASE.md** `v1.0.0` — Desain database lengkap:
  - Konvensi penamaan (UUID, snake_case, audit fields)
  - 28 tabel dalam 8 group (A: Auth, B: Akademik, C: Profesi, D: Alumni, E: Kuesioner, F: Tracer Study, G: Notifikasi, H: Sistem)
  - Skema kolom lengkap dengan tipe data, constraint, dan keterangan
  - Indeks database untuk optimasi query
  - Normalisasi minimal 3NF

- **03_ERD.md** `v1.0.0` — Entity Relationship Diagram:
  - ERD tekstual dengan Crow's Foot Notation
  - Relasi antar semua tabel (1:1, 1:N, N:M)
  - Catatan desain: Immutable Snapshot Pattern, Soft Delete Strategy, UUID Strategy, Audit Field Strategy

- **04_ARCHITECTURE.md** `v1.0.0` — Arsitektur sistem lengkap:
  - Diagram arsitektur keseluruhan (Client → Web Server → Application → Data → Background → External)
  - Struktur direktori proyek lengkap
  - Pola desain: Repository Pattern, Service Layer, Policy & Gate (RBAC), Immutable Snapshot, Queue & Jobs, Audit Trail via Observer
  - Alur autentikasi: Session, Sanctum Token, OTP, Employer Token
  - Deployment architecture (Ubuntu + aaPanel, non-Docker)

- **05_API.md** `v1.0.0` — Struktur API lengkap:
  - Konvensi response (sukses & gagal)
  - HTTP status codes yang digunakan
  - 15 group endpoint (Auth, User, Fakultas, Program Studi, Profesi, Institusi, Alumni Admin, Alumni Self, Permohonan, Kuesioner, Tracer Study, Pengisian Kuesioner, Pelaporan, Pengaturan, Audit & Log)
  - Tabel rate limiting per group endpoint

- **06_UI_UX.md** `v1.0.0` — Desain UI/UX lengkap:
  - Prinsip desain (Profesional, Intuitif, Responsif, Aksesibel, Konsisten)
  - Palet warna: Primary Teal `#0d6c7c`, Accent Gold `#d97706`
  - Tipografi: Plus Jakarta Sans (display) + Inter (body) + JetBrains Mono (monospace)
  - Layout system: 3 layout (Admin, Alumni, Employer)
  - Daftar halaman + routing lengkap
  - 25 komponen reusable Vue (base + domain)
  - 8 Pinia store
  - Routing lengkap: admin, alumni, employer, public routes

- **07_SECURITY.md** `v1.0.0` — Arsitektur keamanan lengkap:
  - Prinsip keamanan: Least Privilege, Defense in Depth, Secure by Default, Fail Safely, Zero Trust, Audit Everything
  - Autentikasi: Session Auth, Sanctum Token Auth, OTP Auth, Employer Access Token
  - RBAC: role matrix, Policy & Gate, middleware auth
  - Perlindungan input: CSRF, XSS, SQL Injection, Mass Assignment, File Upload
  - Rate Limiting: 8 konfigurasi limiter
  - Keamanan password: Hash::make(), aturan password
  - Keamanan session: enkripsi, HttpOnly, Secure, SameSite
  - Keamanan database: proteksi data sensitif, soft delete, audit fields
  - Keamanan API: CORS, Form Request validation, response security
  - Keamanan integrasi: WA Gateway masking, SMTP TLS
  - Audit Trail & Security Logging: 14 security events yang dicatat
  - Keamanan server: Nginx headers, PHP security, file permissions
  - Checklist keamanan per fase

- **08_PHASE_TRACKER.md** `v1.0.0` — Pelacak fase pengembangan:
  - Status semua 9 phase (27 total sesi)
  - Task list lengkap per sesi (backend + frontend)
  - Keputusan teknis penting yang sudah ditetapkan
  - Template catatan per sesi
  - Kolom konflik & masalah

- **09_CHANGELOG.md** `v1.0.0` *(file ini)* — Riwayat perubahan:
  - Panduan format changelog
  - Aturan semantic versioning
  - Entry pertama mendokumentasikan pembuatan semua 9 dokumen knowledge base

#### Keputusan Teknis yang Ditetapkan

- **Stack teknologi final dikonfirmasi:**
  - Backend: Laravel 12, PHP 8.3, MySQL 8+, Sanctum, Policy & Gate, Queue, Scheduler, Notifications
  - Frontend: Vue 3 Composition API, Vite, Tailwind CSS, Pinia, Vue Router, Axios, ApexCharts
  - Export: Laravel Excel, DomPDF
  - Deployment: Ubuntu + aaPanel, Apache/Nginx (non-Docker)

- **UUID CHAR(36) sebagai Primary Key** — mencegah enumerasi ID, mendukung distribusi

- **Queue driver: database** — kemudahan deployment tanpa Redis

- **WA Gateway UNISYA dikonfirmasi:**
  - Endpoint: `https://wacenter.unisya.ac.id/send-message`
  - Method: POST (JSON) dan GET (Query String)
  - Parameter: `api_key`, `sender`, `number`, `message`, `footer`, `msgid`, `full`
  - Mode yang digunakan: POST JSON (lebih aman untuk data sensitif)

- **OTP disimpan dalam bentuk hash (bcrypt)** — plain OTP tidak pernah tersimpan di database

- **Employer token: SHA-256 hash** + token_plain di-null setelah pengiriman

- **Immutable Snapshot Pattern** pada `questionnaire_responses` dan `questionnaire_answers` — perubahan kuesioner tidak merusak data historis

- **SoftDeletes** pada semua tabel master — data tidak pernah dihapus permanen

- **Bahasa Indonesia** untuk semua label UI, menu, laporan, form, pesan validasi

---

## [1.0.1] — 2026-06-05

**Phase:** Phase 1 — Fondasi Sistem  
**Sesi:** Session 1A — Setup Proyek & Infrastruktur  
**Developer:** AI Session

### Added

- Konsolidasi audit status migration Phase 1 Session 1A menjadi dua kelompok: migration yang sudah lulus dan migration yang masih conditional.
- Dokumentasi enum eksplisit `salary_range` pada tabel `alumni_employment_histories` di `02_DATABASE.md` v1.0.1.

### Changed

- `02_DATABASE.md` diperbarui dari versi `1.0.0` menjadi `1.0.1`.
- Bagian desain database diperjelas agar nilai enum kritis tidak ambigu di level migration, request validation, seeders, import Excel, dan laporan analitik.
- Status proyek diperbarui untuk menunjukkan bahwa Phase 1 / Session 1A sedang dalam proses audit dan konsolidasi, bukan lagi “belum dimulai”.

### Fixed

- Menutup ambiguity enum `salary_range` pada tabel `alumni_employment_histories` dengan nilai final: `'<1jt','1-3jt','3-5jt','5-10jt','>10jt'`.
- Menetapkan ulang open findings schema drift pada tabel: `users`, `otp_verifications`, `employer_access_tokens`, `faculties`, `study_programs`, `profession_categories`, `professions`, `institutions`, `institution_details`, `alumni`, `alumni_employment_histories`, `alumni_requests`, `questionnaires`, dan `tracer_studies`.
- Mengonfirmasi migration yang sudah konsisten terhadap dokumen final: `personal_access_tokens`, `questionnaire_categories`, `answer_types`, `questionnaire_questions`, `tracer_study_questionnaires`, `questionnaire_responses`, `questionnaire_answers`, `notifications`, `notification_logs`, `app_settings`, `audit_trails`, `activity_logs`, `jobs`, dan `failed_jobs`.

### Security

- Mengurangi risiko inkonsistensi enum lintas layer yang dapat menyebabkan validasi, seeding, import data, dan pelaporan analitik memakai domain nilai yang berbeda.
- Menjaga konsistensi schema sebagai dasar untuk request validation, authorization, service layer, dan audit trail pada phase lanjutan.

### Breaking

- Penamaan enum final pada beberapa tabel harus dianggap sebagai sumber kebenaran baru untuk implementasi migration, model casts, request validation, seeders, dan test data.
- Implementasi yang masih memakai nilai enum lama seperti `superadmin`, `employeraccess`, `phoneverify`, `emailverify`, `melanjutkanstudi`, `belumbekerja`, `updateakademik`, `updateprofil`, `studyprogram` harus disesuaikan ke format final yang didokumentasikan.

### Catatan Teknis

- Session 1A belum ditutup secara penuh karena migration aktual masih perlu dipatch agar identik dengan `02_DATABASE.md` v1.0.1 sebelum menjalankan migrasi final dan verifikasi database.
- Daftar migration conditional menjadi prioritas patch akhir sebelum melanjutkan ke Session 1B.

---

## [1.0.2] — 2026-06-05

**Phase:** Phase 1 — Fondasi Sistem  
**Sesi:** Session 1A — Setup Proyek & Infrastruktur  
**Developer:** AI Session

### Added
- Patch final 14 migration conditional agar 100% identik dengan `02_DATABASE.md` v1.0.1.
- Migration baru `add_tracer_study_fk_to_employer_access_tokens` untuk menghindari circular dependency.
- Konstanta enum terpusat di `app/Enums/`: `AlumniGender`, `EmploymentStatus`, `SalaryRange`, `JobRelevance`, `InstitutionType`, `OtpPurpose`.
- Model baru: `TracerStudy`, `Questionnaire`, `InstitutionDetail`.
- Model diperbarui: `AlumniEmploymentHistory`, `Institution`, `AlumniRequest`, `EmployerAccessToken`, `Alumni`.

### Changed
- `08_PHASE_TRACKER.md` diperbarui: Session 1A ditandai SELESAI.
- Model `EmployerAccessToken` diperluas dengan field `otp_verified`, `revoked_at`, `tracer_study_id`.
- Model `Institution` diperbarui: enum type final, kolom `logo` menggantikan `logo_path`.
- Model `Alumni` fillable dan cast disesuaikan dengan enum final.

### Fixed
- Enum `gender` pada `alumni`: `laki_laki`, `perempuan` (sebelumnya drift ke `laki-laki`).
- Enum `employment_status` pada `alumni`: `melanjutkan_studi`, `belum_bekerja` (sebelumnya `melanjutkanStudi`).
- Enum `salary_range` pada `alumni_employment_histories`: `<1jt`,`1-3jt`,`3-5jt`,`5-10jt`,`>10jt`.
- Enum `type` pada `alumni_requests`: `update_akademik`, `update_profil`, `lainnya`.
- Enum `scope` pada `questionnaires`: `global`, `faculty`, `study_program`.
- Enum `target_scope` pada `tracer_studies`: `all`, `faculty`, `study_program`.
- Enum `type` pada `institutions`: `pemerintah`, `swasta`, `bumn`, `pendidikan`, `lainnya`.
- Enum `purpose` pada `otp_verifications`: `login`, `employer_access`, `phone_verify`, `email_verify`.
- Kolom `fax` ditambahkan pada `institution_details`.
- Kolom `created_by` ditambahkan pada `institution_details`.
- Kolom `description` ditambahkan pada `faculties`, `study_programs`, `profession_categories`, `professions`.
- `D4` dihapus dari enum `degree_level` pada `study_programs`.
- Soft delete + full audit fields ditambahkan pada `alumni_employment_histories`.
- Soft delete + `otp_verified` + `revoked_at` + `tracer_study_id` ditambahkan pada `employer_access_tokens`.

### Security
- Konstanta enum di `app/Enums/` menjadi single source of truth untuk nilai yang diterima oleh Form Request validation, mencegah nilai arbitrer masuk ke database.

### Breaking
- `alumni.gender`: nilai `laki-laki` (hyphen) sudah tidak valid. Gunakan `laki_laki`.
- `alumni.employment_status`: nilai `melanjutkanStudi`, `belumBekerja` tidak valid. Gunakan underscore.
- `institutions.type`: nilai `ngo`, `wirausaha` tidak valid. Gunakan `pendidikan` atau `lainnya`.
- `institutions`: kolom `city`, `province`, `phone`, `email` dipindahkan ke `institution_details`.

### Catatan Teknis
- Langkah mandatory berikutnya sebelum Session 1B dimulai: jalankan `php artisan migrate` dan verifikasi SHOW CREATE TABLE di MySQL.
- Gunakan konstanta `Model::SALARY_RANGES`, `Model::JOB_RELEVANCES`, dsb., atau `SalaryRange::cases()` untuk validasi `in:` pada Form Request.

---

## [1.0.3] — 2026-06-05

**Phase:** Phase 1 — Fondasi Sistem  
**Sesi:** Session 1A — Setup Proyek & Infrastruktur  
**Developer:** AI Session

### Added

- Bukti eksekusi migration aktual melalui output `php artisan migrate:status`.
- Verifikasi schema aktual database melalui file `CREATE_TABLE.md`.
- Konfirmasi bahwa migration tambahan `2026_06_04_000019_add_tracer_study_fk_to_employer_access_tokens` telah dijalankan dan foreign key `tracerstudyid` benar-benar terpasang pada tabel `employeraccesstokens`.

### Changed

- Status Session 1A diperbarui dari “selesai secara dokumentasi dan implementasi migration” menjadi “selesai terverifikasi melalui migrate status dan schema database aktual”.
- Prasyarat awal Session 1B diubah dari “jalankan php artisan migrate” menjadi “verifikasi kompatibilitas autentikasi dan lanjut implementasi Auth/RBAC”.

### Fixed

- Menutup ketidakpastian apakah seluruh migration benar-benar telah dieksekusi di database.
- Mengonfirmasi bahwa tabel inti proyek (`users`, `alumni`, `alumniemploymenthistories`, `questionnaires`, `tracerstudies`, `questionnaireresponses`, `appsettings`, `audittrails`, `activitylogs`) telah terbentuk di schema aktual.
- Mengonfirmasi bahwa migration Laravel sistem untuk `cache`, `jobs`, `job_batches`, dan `failed_jobs` juga telah berjalan.

### Security

- Validasi schema aktual memperkuat dasar audit keamanan karena foreign key, audit fields, soft delete, dan enum domain utama telah benar-benar ada di database, bukan hanya pada rancangan migration.

### Breaking

- Nilai enum dan naming convention yang sekarang ada di database aktual harus dianggap sebagai source of truth implementasi sampai ada migrasi korektif baru.
- Implementasi Auth API berbasis Sanctum wajib memverifikasi `personal_access_tokens.tokenable_id` karena saat ini masih bertipe `bigint unsigned` sementara `users.id` bertipe `char(36)`.

### Catatan Teknis

- Semua migration pada Session 1A berstatus `Ran` dalam batch 1.
- Session 1A resmi ditutup penuh.
- Langkah pertama Session 1B adalah audit kompatibilitas Sanctum UUID lalu implementasi autentikasi, OTP, dan RBAC.

### Updated
- Status migration `personal_access_tokens` dikonfirmasi **LULUS** setelah verifikasi runtime dengan `SHOW CREATE TABLE`.
- Struktur aktual tabel `personal_access_tokens` telah sesuai dengan `02_DATABASE.md` v1.0.1:
  - `id` = `BIGINT UNSIGNED AUTO_INCREMENT`
  - `tokenable_type` = `VARCHAR(255)`
  - `tokenable_id` = `CHAR(36)`
  - indeks polymorphic (`tokenable_type`, `tokenable_id`) tersedia

### Audit Notes
- Tidak ditemukan data invalid `tokenable_id = '0'` atau `0` pada `personal_access_tokens`.
- Fokus audit Session 1A selanjutnya dipersempit ke migration lain yang masih conditional.

---

## [1.0.4] — 2026-06-05

**Phase:** Phase 1 — Fondasi Sistem  
**Sesi:** Session 1A — Setup Proyek & Infrastruktur  
**Developer:** AI Session

### Added

- Final source alignment review untuk seluruh migration yang sebelumnya berstatus conditional.
- Catatan implementasi bahwa foreign key `employer_access_tokens.tracer_study_id` dapat dipasang melalui migration tambahan untuk menghindari circular dependency.

### Changed

- Migration `users` diselaraskan ke enum role final `super_admin`, `alumni`.
- Migration `faculties`, `profession_categories`, dan `professions` dipastikan memiliki kolom `description`.
- Migration `institution_details` diselaraskan ke struktur final dengan kolom `city`, `province`, `phone`, `fax`, `email`, `created_by`, dan `updated_by`.
- Terminologi dokumentasi dirapikan agar konsisten memakai istilah Employer (Pengguna Alumni) tanpa menjadikannya role teknis pada tabel `users`.

### Fixed

- Menutup sisa drift source code migration terhadap `02_DATABASE.md` pada tabel conditional inti.
- Menyatukan keputusan bahwa enum final dan penamaan kolom di dokumen database menjadi sumber kebenaran implementasi migration.

### Security

- Mengurangi risiko mismatch lintas layer antara migration, request validation, seeder, import Excel, dan analytics akibat domain enum atau struktur kolom yang tidak sinkron.
- Memperkuat dasar keamanan dan audit karena schema final kini lebih konsisten terhadap dokumen database kanonik.

### Breaking

- Semua implementasi yang masih memakai `superadmin` wajib diganti menjadi `super_admin`.
- Relasi `tracer_study_id` pada `employer_access_tokens` harus mengikuti strategi migration tambahan bila project mempertahankan urutan migrasi saat ini.

### Catatan Teknis

- `02_DATABASE.md` direkomendasikan naik ke versi `1.0.2` untuk mencatat konsistensi istilah dan catatan implementasi foreign key `tracer_study_id`.
- Session 1B hanya boleh dimulai setelah file migration aktif proyek benar-benar identik dengan patch source alignment final ini.

---

## [1.0.5] — 2026-06-05

**Phase:** Phase 1 — Fondasi Sistem  
**Sesi:** Patch Konsolidasi Session 1A / Prasyarat Session 1B  
**Developer:** AI Session

### Added

- Audit konsolidasi final khusus enum `users.role` untuk menyatukan penamaan teknis role admin lintas database, model, gate, seeder, factory, validation, dan dokumentasi proyek.
- Daftar file terdampak yang wajib diselaraskan dari `superadmin` ke `super_admin`.

### Changed

- Nilai enum teknis final untuk `users.role` diubah dari `superadmin` menjadi `super_admin`.
- Pemisahan diperjelas antara label bisnis **Super Admin** untuk UI/dokumentasi naratif dan enum teknis `super_admin` untuk database serta source code.
- Semua helper, seeder, factory, policy, gate, validation rule, dan dokumentasi terkait role admin harus mengacu ke `super_admin`.

### Fixed

- Menghilangkan potensi drift antara migration, model helper, gate/policy, seeder, factory, dan request validation akibat penggunaan campuran `superadmin` dan `super_admin`.
- Memperjelas source of truth untuk implementasi autentikasi dan RBAC sebelum Session 1B dimulai.

### Security

- Konsistensi enum role memperkecil risiko authorization mismatch, terutama pada Gate, Policy, dan seed data akun admin awal.
- Menjaga agar role-based access control tetap deterministik di seluruh layer aplikasi.

### Breaking

- Semua implementasi yang masih memakai string `superadmin` sebagai nilai teknis role wajib diganti menjadi `super_admin`.
- Data seed, validasi request, policy check, dan migration users lama tidak lagi dianggap valid bila masih memakai `superadmin`.

### Catatan Teknis

- Gate dapat tetap bernama `admin`, tetapi logika internal harus mengacu ke helper/model yang membaca role `super_admin`.
- Method helper `isSuperAdmin()` dipertahankan sebagai nama domain, dengan pembacaan nilai enum teknis baru `super_admin`.
- Session 1B hanya boleh dilanjutkan setelah tidak ada lagi referensi teknis `superadmin` pada codebase aktif dan dokumen source of truth.

---

## [1.0.6] — 2026-06-05

**Phase:** Phase 1 — Fondasi Sistem  
**Sesi:** Session 1A Closed, Session 1B Started  
**Developer:** AI Session

### Added

- Konsolidasi status proyek yang menandai Session 1A selesai dan Session 1B sebagai sesi aktif.
- Verifikasi terhadap migration aktif `users` sebagai sumber implementasi aktual untuk fondasi autentikasi.
- Draft patch auth untuk `User`, `UserPolicy`, `AuthService`, auth controllers, middleware auth, JSON exception handling, Pinia auth store, dan router frontend.

### Changed

- Status `08_PHASE_TRACKER.md` diperbarui dari Session 1A ke Session 1B.
- Penilaian risiko proyek diperbarui: risiko enum role pada tabel `users` dinyatakan tertutup.
- Fokus pekerjaan bergeser dari konsolidasi fondasi proyek ke sinkronisasi Authentication & Authorization foundation.

### Fixed

- Drift kritis pada schema `users` tidak lagi menjadi blocker karena migration aktif telah menggunakan:
  - `email_verified_at`
  - `phone_verified_at`
  - `remember_token`
  - enum role `super_admin`, `alumni`
- Penamaan role admin distandarkan ke `super_admin` untuk alignment backend–frontend.
- Patch auth disiapkan agar policy, gate, middleware, controller, service, store, dan router tidak lagi memakai bentuk lama `superadmin`.

### Security

- Menegaskan kembali penggunaan RBAC berbasis gate/policy dengan role final `super_admin`.
- Menjaga konsistensi response JSON auth error untuk `401`, `403`, `422`, `429`, dan `500`.
- Mempertahankan pembatasan akses employer melalui Sanctum token ability `employer`.

### Notes

- Session 1A ditutup karena fondasi setup proyek dan blocker migration `users` telah terselesaikan.
- Session 1B telah dimulai namun status akhirnya bergantung pada apakah patch auth backend dan frontend sudah diterapkan ke codebase aktif dan diuji end-to-end.
- Risiko yang tersisa saat ini bersifat lokal: persistence auth state frontend lintas refresh dan sinkronisasi migration non-`users` untuk flow lanjutan employer/auth.

---

## [1.0.7] — 2026-06-05

**Phase:** Phase 1 — Fondasi Sistem  
**Sesi:** Session 1B Closed, Session 1C Started  
**Developer:** AI Session

### Added

- Penutupan resmi Session 1B setelah patch auth backend dan frontend diterapkan ke codebase aktif.
- Aktivasi Session 1C sebagai sesi aktif baru untuk layout utama, navigasi, dan routing shell aplikasi.
- Catatan transisi bahwa verifikasi auth state saat refresh browser dan smoke test flow auth/employer menjadi QA awal Session 1C, bukan blocker Session 1B.


### Changed

- `08_PHASE_TRACKER.md` diperbarui: Session 1B diubah dari `In Progress` menjadi `Selesai`.
- `08_PHASE_TRACKER.md` diperbarui: Session aktif bergeser dari `Session 1B — Authentication & Authorization Foundation` ke `Session 1C — Layout Utama & Routing`.
- Total progress proyek diperbarui menjadi `2 / 27 sesi`.
- Fokus phase bergeser dari fondasi autentikasi ke fondasi shell UI dan routing aplikasi.

### Fixed

- Menutup status menggantung Session 1B yang sebelumnya masih menunggu konfirmasi apakah patch auth benar-benar sudah diterapkan ke project aktif.
- Menyatukan sumber status implementasi antara tracker proyek dan kondisi codebase aktual setelah patch auth dinyatakan telah diterapkan.
- Menghapus ambiguity operasional apakah auth foundation masih berupa draft atau sudah menjadi implementasi aktif.

### Security

- Fondasi auth yang telah diterapkan mempertahankan RBAC berbasis gate/policy dengan role final `superadmin` dan `alumni`.
- Middleware auth dan pembatasan akses employer tetap menjadi basis kontrol akses sebelum layout serta menu role-based dibangun di Session 1C.
- Risiko utama keamanan bergeser dari mismatch auth logic ke validasi perilaku session/token saat refresh browser dan redirect guard frontend.

### Breaking

- Session 1C harus menganggap auth backend/frontend saat ini sebagai baseline aktif; semua layout, menu, route guard, dan redirect dashboard wajib mengikuti kontrak role dan response auth yang sudah diterapkan.
- Implementasi layout atau router baru tidak boleh memperkenalkan ulang asumsi role lama atau mem-bypass gate/policy yang sudah distandarkan.

### Catatan Teknis

- Session 1B ditutup berdasarkan konfirmasi bahwa patch auth yang telah disusun sebelumnya sudah resmi diterapkan di project.
- QA ringan yang masih tersisa untuk transisi ke Session 1C: verifikasi perilaku auth state lintas refresh dan smoke test end-to-end login, OTP, logout, `me`, serta employer OTP.
- Langkah audit berikutnya adalah memeriksa kesiapan shell UI, route protection, dan konsistensi layout terhadap `06_UI_UX.md`, `04_ARCHITECTURE.md`, dan `07_SECURITY.md`.

---

## [1.0.8] — 2026-06-05

**Phase:** Phase 1 — Fondasi Sistem  
**Sesi:** Session 1C In Progress  
**Developer:** AI Session

### Added
- Final code patch frontend untuk integrasi autentikasi pada `resources/js/stores/auth.js`.
- Final code patch UI store untuk theme hydration pada `resources/js/stores/ui.js`.
- Final code patch bootstrap aplikasi pada `resources/js/app.js`.
- Final code patch route guard pada `resources/js/router/index.js`.
- Final code patch halaman `LoginPage.vue` untuk login email/password dan request OTP.
- Final code patch halaman `OtpPage.vue` untuk verifikasi OTP 6 digit, countdown, dan resend OTP.

### Changed
- Mekanisme autentikasi frontend kini disiapkan untuk menggunakan kontrak API resmi: `login`, `otp/request`, `otp/verify`, `logout`, dan `auth/me`.
- Bootstrap SPA diperbarui agar auth state dapat direhidrasi kembali saat halaman di-refresh.
- Route guard diperbarui agar menunggu proses bootstrap auth sebelum melakukan redirect.

### Fixed
- Menutup gap antara router guard dan store auth yang sebelumnya berpotensi menyebabkan redirect salah saat refresh.
- Menutup gap antara halaman auth statis dan kebutuhan integrasi API autentikasi.
- Menambahkan dukungan OTP countdown dan resend flow sesuai desain UI/UX.

### Notes
- Patch ini disusun sebagai lanjutan Session 1C dan masih memerlukan penempelan ke codebase lokal serta pengujian end-to-end sebelum status sesi dapat dinaikkan menjadi selesai.

---

## TEMPLATE ENTRY CHANGELOG

*Salin template di bawah untuk setiap entry baru. Isi setelah sesi development selesai.*

```
---

## [X.Y.Z] — YYYY-MM-DD

**Phase:** Phase N — Nama Phase
**Sesi:** Session NA / NB / NC
**Developer:** [Nama/AI Session]

### Added
-

### Changed
-

### Fixed
-

### Security
-

### Breaking
-

### Catatan Teknis
-
```

---

## RIWAYAT VERSI DOKUMENTASI

| File | Versi Saat Ini | Terakhir Diubah | Diubah Oleh |
|------|---------------|-----------------|-------------|
| 01_BLUEPRINT.md | 1.0.0 | 2026-06-04 | Initial Creation |
| 02_DATABASE.md | 1.0.0 | 2026-06-04 | Initial Creation |
| 03_ERD.md | 1.0.0 | 2026-06-04 | Initial Creation |
| 04_ARCHITECTURE.md | 1.0.0 | 2026-06-04 | Initial Creation |
| 05_API.md | 1.0.0 | 2026-06-04 | Initial Creation |
| 06_UI_UX.md | 1.0.0 | 2026-06-04 | Initial Creation |
| 07_SECURITY.md | 1.0.0 | 2026-06-04 | Initial Creation |
| 08_PHASE_TRACKER.md | 1.0.0 | 2026-06-04 | Initial Creation |
| 09_CHANGELOG.md | 1.0.0 | 2026-06-04 | Initial Creation |

---

*Setiap perubahan pada dokumentasi atau kode wajib dicatat di file ini. File ini adalah audit trail proyek.*
