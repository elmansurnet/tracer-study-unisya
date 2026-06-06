# 02_DATABASE.md — Desain Database Tracer Study UNISYA

**Versi:** 1.2.0
**Tanggal Dibuat:** 2026-06-05
**Tanggal Diperbarui:** 2026-06-06
**Database Engine:** MySQL 8.0+
**Charset:** utf8mb4
**Collation:** utf8mb4_unicode_ci

---

## RIWAYAT REVISI

| Versi | Tanggal | Perubahan |
|-------|---------|-----------|
| 1.0.0 | 2026-06-04 | Versi awal — semua PK/FK menggunakan CHAR(36) UUID |
| 1.0.1 | 2026-06-05 | Koreksi enum role users; catatan nullable tracer_study_id |
| 1.0.2 | 2026-06-05 | Finalisasi enum role 'super_admin','alumni'; istilah Employer; catatan FK circular dependency |
| 1.1.0 | 2026-06-06 | BREAKING (salah): Koreksi PK/FK ke CHAR(26) ULID — ternyata bertentangan dengan implementasi aktual Phase 1-3 |
| **1.2.0** | **2026-06-06** | **REVERT + FIX: Standar tunggal kembali ke UUID CHAR(36) — sesuai implementasi aktual `$table->uuid()` di seluruh model Phase 1–3. Semua tabel baru (Phase 4+) wajib mengikuti standar ini.** |

---

## KEPUTUSAN TEKNIS — PRIMARY KEY & FOREIGN KEY

> **Keputusan Final (2026-06-06, v1.2.0):** Seluruh Primary Key dan Foreign Key menggunakan **UUID** yang disimpan sebagai **`CHAR(36)`**, bukan ULID CHAR(26).
>
> **Dasar Keputusan:**
> - Semua model aktif Phase 1–3 menggunakan `$this->uuid = true` pada trait `HasUuids` (`Illuminate\Database\Eloquent\Concerns\HasUuids`)
> - Migration aktif Phase 1–3 menggunakan `$table->uuid('id')->primary()`
> - FK ke tabel Phase 1–3 harus `$table->uuid('{ref}_id')->nullable()` → CHAR(36)
> - Mengubah ke ULID akan **break** semua Phase 1–3 yang sudah proven dan berjalan
> - Konsistensi > efisiensi teknis
>
> **Aturan untuk tabel baru (Phase 4+):**
> - PK: `$table->uuid('id')->primary()` → CHAR(36)
> - FK ke tabel lama: `$table->uuid('{ref}_id')->nullable()` → CHAR(36)
> - FK antar tabel baru: `$table->uuid('{ref}_id')->nullable()` → CHAR(36)
> - Model wajib menggunakan `use HasUuids;` dari `Illuminate\Database\Eloquent\Concerns\HasUuids`
>
> **Tabel yang TIDAK menggunakan UUID (pengecualian resmi):**
> - `personal_access_tokens` — PK adalah BIGINT AUTO_INCREMENT (Laravel Sanctum default)
> - `tracer_study_questionnaires` — tabel pivot, menggunakan composite PK
> - `notifications` — PK adalah CHAR(36) UUID via Laravel default Notification
> - `jobs`, `failed_jobs`, `cache`, `sessions` — tabel Laravel bawaan

---

## KONVENSI PENAMAAN

- Nama tabel: `snake_case`, bentuk jamak
- Nama kolom: `snake_case`
- **Primary Key: `id` (UUID, CHAR(36))** — menggunakan `$table->uuid('id')->primary()`
- **Foreign Key: `{tabel_referensi_singular}_id` (CHAR(36))** — menggunakan `$table->uuid('{ref}_id')`
- Audit fields wajib: `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`
- Normalisasi minimal: 3NF
- Named index: `idx_{tabel_singkat}_{kolom}`, Named FK: `fk_{tabel_singkat}_{kolom}`

---

## DAFTAR TABEL

### GROUP A: AUTENTIKASI & PENGGUNA

---

#### A1. `users`
Menyimpan akun pengguna sistem (Super Admin dan Alumni).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK, NOT NULL | **UUID** |
| name | VARCHAR(255) | NOT NULL | Nama lengkap |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Email login |
| email_verified_at | TIMESTAMP | NULL | Waktu verifikasi email |
| password | VARCHAR(255) | NOT NULL | Hash bcrypt |
| phone | VARCHAR(20) | NULL | Nomor WhatsApp |
| phone_verified_at | TIMESTAMP | NULL | Waktu verifikasi WA |
| role | ENUM | NOT NULL | `'super_admin', 'alumni'` |
| is_active | TINYINT(1) | DEFAULT 1 | Status aktif akun |
| last_login_at | TIMESTAMP | NULL | Waktu login terakhir |
| last_login_ip | VARCHAR(45) | NULL | IP login terakhir |
| remember_token | VARCHAR(100) | NULL | Token remember me |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

**Indeks:** `email` (UNIQUE), `role`, `is_active`, `deleted_at`

---

#### A2. `personal_access_tokens`
Token Sanctum untuk API authentication (Laravel default).

> ⚠️ **Pengecualian:** PK adalah `BIGINT UNSIGNED AUTO_INCREMENT` (standar Laravel Sanctum).
> `tokenable_id` menggunakan `CHAR(36)` agar kompatibel dengan UUID users.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | BIGINT UNSIGNED | PK AUTO_INCREMENT | — |
| tokenable_type | VARCHAR(255) | NOT NULL | Polymorphic type |
| tokenable_id | CHAR(36) | NOT NULL | **UUID** — ID model (users) |
| name | VARCHAR(255) | NOT NULL | Nama token |
| token | VARCHAR(64) | UNIQUE NOT NULL | Hash token |
| abilities | TEXT | NULL | JSON abilities |
| last_used_at | TIMESTAMP | NULL | — |
| expires_at | TIMESTAMP | NULL | — |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |

**Catatan implementasi:** Migration `2026_06_05_000001_fix_personal_access_tokens_tokenable_id_to_char36.php` memastikan `tokenable_id` adalah `CHAR(36)` agar kompatibel dengan UUID.

---

#### A3. `otp_verifications`
Menyimpan OTP untuk verifikasi alumni dan employer.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| identifier | VARCHAR(255) | NOT NULL | Email atau nomor HP |
| identifier_type | ENUM | NOT NULL | `'email', 'whatsapp'` |
| otp_code | VARCHAR(10) | NOT NULL | Kode OTP (hashed bcrypt) |
| purpose | ENUM | NOT NULL | `'login', 'employer_access', 'phone_verify', 'email_verify'` |
| reference_id | CHAR(36) | NULL | ID referensi (employer_access_tokens.id, dsb) |
| attempts | TINYINT | DEFAULT 0 | Jumlah percobaan |
| max_attempts | TINYINT | DEFAULT 5 | Batas maksimal percobaan |
| is_used | TINYINT(1) | DEFAULT 0 | Sudah digunakan? |
| expires_at | TIMESTAMP | NOT NULL | Waktu kadaluarsa (5 menit) |
| used_at | TIMESTAMP | NULL | Waktu digunakan |
| ip_address | VARCHAR(45) | NULL | IP peminta |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |

**Indeks:** `identifier`, `purpose`, `expires_at`, `is_used`

---

#### A4. `employer_access_tokens`
Token akses khusus untuk Employer (Pengguna Alumni) — tanpa registrasi.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| alumni_id | CHAR(36) | FK alumni.id, NOT NULL | Alumni yang mengundang |
| institution_id | CHAR(36) | FK institutions.id, NOT NULL | Institusi employer |
| contact_name | VARCHAR(255) | NOT NULL | Nama kontak employer |
| contact_phone | VARCHAR(20) | NULL | Nomor WA employer |
| contact_email | VARCHAR(255) | NULL | Email employer |
| token | VARCHAR(64) | UNIQUE, NOT NULL | Token unik (hashed SHA-256) |
| token_plain | VARCHAR(64) | NULL | Plain token untuk pengiriman (di-null setelah dikirim) |
| is_used | TINYINT(1) | DEFAULT 0 | Sudah digunakan? |
| is_revoked | TINYINT(1) | DEFAULT 0 | Dicabut oleh admin? |
| otp_verified | TINYINT(1) | DEFAULT 0 | OTP sudah diverifikasi? |
| expires_at | TIMESTAMP | NULL | Waktu kadaluarsa |
| used_at | TIMESTAMP | NULL | Waktu pertama akses |
| revoked_at | TIMESTAMP | NULL | Waktu pencabutan |
| revoked_by | CHAR(36) | NULL, FK users.id | Yang mencabut |
| tracer_study_id | CHAR(36) | NULL, FK tracer_studies.id | Sesi tracer study terkait |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

**Indeks:** `token` (UNIQUE), `alumni_id`, `institution_id`, `is_used`, `is_revoked`, `expires_at`

**Catatan implementasi:** FK `tracer_study_id -> tracer_studies.id` dipasang via migration terpisah `2026_06_04_000019_add_tracer_study_fk_to_employer_access_tokens.php` untuk menghindari circular dependency urutan migrasi.

---

### GROUP B: DATA AKADEMIK

---

#### B1. `faculties`
Data Fakultas UNISYA.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| code | VARCHAR(20) | UNIQUE, NOT NULL | Kode fakultas |
| name | VARCHAR(255) | NOT NULL | Nama fakultas |
| description | TEXT | NULL | Deskripsi |
| is_active | TINYINT(1) | DEFAULT 1 | Status aktif |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

**Catatan migrasi:** File aktif adalah `2026_06_04_000004_create_faculties_table.php`. File `2025_01_03_000001_create_faculties_table.php` adalah duplikat lama dan harus dihapus.

---

#### B2. `study_programs`
Data Program Studi UNISYA.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| faculty_id | CHAR(36) | FK faculties.id, NOT NULL | Fakultas induk |
| code | VARCHAR(20) | UNIQUE, NOT NULL | Kode program studi |
| name | VARCHAR(255) | NOT NULL | Nama program studi |
| degree_level | ENUM | NOT NULL | `'D3', 'S1', 'S2', 'S3', 'Profesi'` |
| description | TEXT | NULL | Deskripsi |
| is_active | TINYINT(1) | DEFAULT 1 | Status aktif |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

**Catatan migrasi:** File aktif adalah `2026_06_04_000005_create_study_programs_table.php`. File `2025_01_03_000002_create_study_programs_table.php` adalah duplikat lama dan harus dihapus.

---

### GROUP C: DATA PROFESI & INSTITUSI

---

#### C1. `profession_categories`
Kategori profesi/bidang pekerjaan.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| name | VARCHAR(255) | NOT NULL | Nama kategori |
| description | TEXT | NULL | Deskripsi |
| is_active | TINYINT(1) | DEFAULT 1 | — |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

---

#### C2. `professions`
Data profesi spesifik dalam suatu kategori.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| profession_category_id | CHAR(36) | FK profession_categories.id, NOT NULL | Kategori induk |
| name | VARCHAR(255) | NOT NULL | Nama profesi |
| description | TEXT | NULL | Deskripsi |
| is_active | TINYINT(1) | DEFAULT 1 | — |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

---

#### C3. `institutions`
Data institusi/perusahaan pengguna alumni.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| name | VARCHAR(255) | NOT NULL | Nama institusi |
| type | ENUM | NOT NULL | `'pemerintah', 'swasta', 'bumn', 'pendidikan', 'lainnya'` |
| sector | VARCHAR(255) | NULL | Sektor/bidang usaha |
| website | VARCHAR(255) | NULL | URL website |
| logo | VARCHAR(255) | NULL | Path logo |
| is_active | TINYINT(1) | DEFAULT 1 | — |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

---

#### C4. `institution_details`
Detail kontak dan lokasi institusi.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| institution_id | CHAR(36) | FK institutions.id, UNIQUE NOT NULL | Satu institusi satu detail |
| address | TEXT | NULL | Alamat lengkap |
| city | VARCHAR(100) | NULL | Kota |
| province | VARCHAR(100) | NULL | Provinsi |
| postal_code | VARCHAR(10) | NULL | Kode pos |
| phone | VARCHAR(20) | NULL | Nomor telepon |
| fax | VARCHAR(20) | NULL | Nomor fax |
| email | VARCHAR(255) | NULL | Email resmi |
| contact_person | VARCHAR(255) | NULL | Nama PIC |
| contact_phone | VARCHAR(20) | NULL | HP PIC |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |

---

### GROUP D: DATA ALUMNI

---

#### D1. `alumni`
Data lengkap alumni UNISYA.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| user_id | CHAR(36) | FK users.id, UNIQUE NULL | Akun user (nullable jika belum punya akun) |
| study_program_id | CHAR(36) | FK study_programs.id, NOT NULL | Program studi |
| nim | VARCHAR(50) | UNIQUE, NOT NULL | Nomor Induk Mahasiswa |
| name | VARCHAR(255) | NOT NULL | Nama lengkap |
| gender | ENUM | NOT NULL | `'laki_laki', 'perempuan'` |
| birth_place | VARCHAR(100) | NULL | Tempat lahir |
| birth_date | DATE | NULL | Tanggal lahir |
| address | TEXT | NULL | Alamat tinggal |
| city | VARCHAR(100) | NULL | Kota domisili |
| province | VARCHAR(100) | NULL | Provinsi |
| postal_code | VARCHAR(10) | NULL | Kode pos |
| phone | VARCHAR(20) | NULL | Nomor WA |
| email | VARCHAR(255) | NULL | Email |
| graduation_year | YEAR | NOT NULL | Tahun lulus |
| graduation_date | DATE | NULL | Tanggal wisuda |
| ipk | DECIMAL(4,2) | NULL | IPK |
| thesis_title | TEXT | NULL | Judul skripsi/tesis |
| photo | VARCHAR(255) | NULL | Path foto |
| is_employed | TINYINT(1) | DEFAULT 0 | Status bekerja saat ini |
| employment_status | ENUM | NULL | `'bekerja', 'wirausaha', 'melanjutkan_studi', 'belum_bekerja'` |
| waiting_period_months | SMALLINT | NULL | Masa tunggu kerja (bulan) |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

**Indeks:** `nim` (UNIQUE), `user_id`, `study_program_id`, `graduation_year`, `employment_status`

---

#### D2. `alumni_employment_histories`
Riwayat pekerjaan alumni (employment tracking).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| alumni_id | CHAR(36) | FK alumni.id, NOT NULL | Alumni terkait |
| institution_id | CHAR(36) | FK institutions.id, NULL | Institusi tempat bekerja |
| profession_id | CHAR(36) | FK professions.id, NULL | Profesi/jabatan |
| job_title | VARCHAR(255) | NULL | Judul jabatan spesifik |
| start_date | DATE | NOT NULL | Tanggal mulai bekerja |
| end_date | DATE | NULL | Tanggal berakhir (null = saat ini) |
| is_current | TINYINT(1) | DEFAULT 0 | Pekerjaan saat ini? |
| salary_range | ENUM | NULL | `'<1jt','1-3jt','3-5jt','5-10jt','>10jt'` |
| job_relevance | ENUM | NULL | `'sangat_relevan','relevan','kurang_relevan','tidak_relevan'` |
| notes | TEXT | NULL | Catatan |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

---

#### D3. `alumni_requests`
Permohonan update data akademik oleh alumni.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| alumni_id | CHAR(36) | FK alumni.id, NOT NULL | Alumni pemohon |
| type | ENUM | NOT NULL | `'update_akademik', 'update_profil', 'lainnya'` |
| field_name | VARCHAR(100) | NOT NULL | Field yang diminta diubah |
| old_value | TEXT | NULL | Nilai lama |
| new_value | TEXT | NOT NULL | Nilai baru yang diminta |
| reason | TEXT | NULL | Alasan permohonan |
| status | ENUM | DEFAULT 'menunggu' | `'menunggu', 'disetujui', 'ditolak'` |
| reviewed_by | CHAR(36) | NULL, FK users.id | Yang mereview |
| reviewed_at | TIMESTAMP | NULL | Waktu review |
| review_notes | TEXT | NULL | Catatan reviewer |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

---

### GROUP E: MESIN KUESIONER

---

#### E1. `questionnaire_categories`
Kategori/kelompok kuesioner.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| name | VARCHAR(255) | NOT NULL | Nama kategori |
| description | TEXT | NULL | — |
| is_active | TINYINT(1) | DEFAULT 1 | — |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

---

#### E2. `answer_types`
Tipe jawaban yang tersedia.

> ⚠️ Tabel ini **tidak menggunakan SoftDeletes** (tidak ada `deleted_at`, `deleted_by`).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| code | VARCHAR(50) | UNIQUE, NOT NULL | `'true_false', 'scale_1_5', 'scale_1_10'` |
| name | VARCHAR(100) | NOT NULL | Nama tampilan |
| description | TEXT | NULL | — |
| config | JSON | NULL | Konfigurasi tipe (min, max, labels) |
| is_active | TINYINT(1) | DEFAULT 1 | — |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |

---

#### E3. `questionnaires`
Master kuesioner.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| questionnaire_category_id | CHAR(36) | FK questionnaire_categories.id, NOT NULL | Kategori |
| title | VARCHAR(255) | NOT NULL | Judul kuesioner |
| description | TEXT | NULL | Deskripsi |
| respondent_type | ENUM | NOT NULL | `'alumni', 'employer', 'both'` |
| scope | ENUM | NOT NULL | `'global', 'faculty', 'study_program'` |
| faculty_id | CHAR(36) | NULL, FK faculties.id | Jika scope = faculty |
| study_program_id | CHAR(36) | NULL, FK study_programs.id | Jika scope = study_program |
| start_date | DATE | NULL | Tanggal aktif mulai |
| end_date | DATE | NULL | Tanggal aktif berakhir |
| is_active | TINYINT(1) | DEFAULT 1 | — |
| version | SMALLINT | DEFAULT 1 | Versi kuesioner |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

---

#### E4. `questionnaire_questions`
Pertanyaan dalam kuesioner.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| questionnaire_id | CHAR(36) | FK questionnaires.id, NOT NULL | Kuesioner induk |
| answer_type_id | CHAR(36) | FK answer_types.id, NOT NULL | Tipe jawaban |
| question_text | TEXT | NOT NULL | Teks pertanyaan |
| question_order | SMALLINT | DEFAULT 0 | Urutan tampil |
| is_required | TINYINT(1) | DEFAULT 1 | Wajib diisi? |
| is_active | TINYINT(1) | DEFAULT 1 | — |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

---

### GROUP F: TRACER STUDY & RESPONS

---

#### F1. `tracer_studies`
Sesi Tracer Study.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| title | VARCHAR(255) | NOT NULL | Judul sesi |
| description | TEXT | NULL | — |
| academic_year | VARCHAR(20) | NOT NULL | Tahun akademik (2024/2025) |
| start_date | DATE | NOT NULL | Tanggal mulai |
| end_date | DATE | NOT NULL | Tanggal berakhir |
| status | ENUM | DEFAULT 'draft' | `'draft', 'aktif', 'selesai', 'dibatalkan'` |
| target_scope | ENUM | DEFAULT 'all' | `'all', 'faculty', 'study_program'` |
| target_faculty_id | CHAR(36) | NULL, FK faculties.id | — |
| target_study_program_id | CHAR(36) | NULL, FK study_programs.id | — |
| target_graduation_years | JSON | NULL | Array tahun lulus target |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

---

#### F2. `tracer_study_questionnaires`
Pivot: kuesioner yang terlibat dalam suatu sesi tracer study.

> ⚠️ Tabel pivot ini **tidak menggunakan SoftDeletes** dan **tidak memiliki audit fields** (created_by/updated_by).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| tracer_study_id | CHAR(36) | FK tracer_studies.id, NOT NULL | Sesi tracer |
| questionnaire_id | CHAR(36) | FK questionnaires.id, NOT NULL | Kuesioner |
| order | SMALLINT | DEFAULT 0 | Urutan tampil |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |

**PRIMARY KEY:** (`tracer_study_id`, `questionnaire_id`) — composite PK, tidak ada kolom `id` terpisah

---

#### F3. `questionnaire_responses`
Header respons kuesioner (satu baris per alumni/employer per kuesioner per sesi).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| tracer_study_id | CHAR(36) | FK tracer_studies.id, NULL | Sesi tracer (null = diluar sesi) |
| questionnaire_id | CHAR(36) | FK questionnaires.id, NOT NULL | Kuesioner yang diisi |
| respondent_type | ENUM | NOT NULL | `'alumni', 'employer'` |
| alumni_id | CHAR(36) | NULL, FK alumni.id | Jika alumni |
| employer_access_token_id | CHAR(36) | NULL, FK employer_access_tokens.id | Jika employer |
| submitted_at | TIMESTAMP | NOT NULL | Waktu submit |
| ip_address | VARCHAR(45) | NULL | IP pengisi |
| questionnaire_snapshot | JSON | NOT NULL | **SNAPSHOT** struktur kuesioner saat diisi |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |

**UNIQUE:** (`tracer_study_id`, `questionnaire_id`, `alumni_id`) — mencegah duplikat alumni
**UNIQUE:** (`tracer_study_id`, `questionnaire_id`, `employer_access_token_id`) — mencegah duplikat employer
**Indeks:** `tracer_study_id`, `questionnaire_id`, `respondent_type`, `alumni_id`

---

#### F4. `questionnaire_answers`
Jawaban individual per pertanyaan (immutable setelah submit).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| questionnaire_response_id | CHAR(36) | FK questionnaire_responses.id, NOT NULL | Header respons |
| question_snapshot | JSON | NOT NULL | **SNAPSHOT** teks pertanyaan saat diisi |
| answer_type_snapshot | JSON | NOT NULL | **SNAPSHOT** konfigurasi tipe jawaban saat diisi |
| answer_value | VARCHAR(255) | NOT NULL | Nilai jawaban |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |

**Indeks:** `questionnaire_response_id`

---

### GROUP G: SISTEM NOTIFIKASI

---

#### G1. `notifications`
Notifikasi in-app (Laravel default morphable notifications).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** — standar Laravel Notification |
| type | VARCHAR(255) | NOT NULL | Class notifikasi Laravel |
| notifiable_type | VARCHAR(255) | NOT NULL | Polymorphic type |
| notifiable_id | CHAR(36) | NOT NULL | **UUID** — ID model (users/alumni) |
| data | JSON | NOT NULL | Isi notifikasi |
| read_at | TIMESTAMP | NULL | Waktu dibaca |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |

> ⚠️ **Pengecualian:** PK menggunakan `CHAR(36) UUID` karena ini adalah tabel Laravel bawaan (`Illuminate\Notifications\DatabaseNotification`). Tidak dapat diubah tanpa menimpa trait bawaan Laravel.

---

#### G2. `notification_logs`
Log pengiriman WA dan Email untuk monitoring.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| channel | ENUM | NOT NULL | `'whatsapp', 'email'` |
| recipient | VARCHAR(255) | NOT NULL | Nomor WA atau email |
| subject | VARCHAR(255) | NULL | Subjek (email) |
| message | TEXT | NOT NULL | Isi pesan |
| status | ENUM | DEFAULT 'pending' | `'pending', 'terkirim', 'gagal'` |
| provider_response | JSON | NULL | Respons API WA/SMTP |
| error_message | TEXT | NULL | Pesan error jika gagal |
| retries | TINYINT | DEFAULT 0 | Jumlah percobaan ulang |
| sent_at | TIMESTAMP | NULL | Waktu terkirim |
| reference_type | VARCHAR(100) | NULL | Polymorphic: model terkait |
| reference_id | CHAR(36) | NULL | **UUID** — ID model terkait |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |

---

### GROUP H: PENGATURAN & SISTEM

---

#### H1. `app_settings`
Konfigurasi sistem yang dapat diubah via UI.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| group | VARCHAR(100) | NOT NULL | `'general', 'wa_gateway', 'smtp', 'security', 'notification'` |
| key | VARCHAR(100) | NOT NULL | Kunci setting |
| value | TEXT | NULL | Nilai setting |
| type | ENUM | DEFAULT 'string' | `'string', 'boolean', 'integer', 'json', 'password'` |
| label | VARCHAR(255) | NULL | Label tampilan UI |
| description | TEXT | NULL | Keterangan |
| is_encrypted | TINYINT(1) | DEFAULT 0 | Nilanya terenkripsi? |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |

**UNIQUE:** (`group`, `key`)

---

#### H2. `audit_trails`
Mencatat setiap operasi CRUD pada data penting.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| user_id | CHAR(36) | NULL, FK users.id | Pengguna yang melakukan aksi |
| user_type | ENUM | NULL | `'user', 'employer', 'system'` |
| event | VARCHAR(50) | NOT NULL | `'created', 'updated', 'deleted', 'restored'` |
| auditable_type | VARCHAR(255) | NOT NULL | Model yang diaudit |
| auditable_id | CHAR(36) | NOT NULL | **UUID** — ID record yang diaudit |
| old_values | JSON | NULL | Nilai sebelum perubahan |
| new_values | JSON | NULL | Nilai setelah perubahan |
| url | VARCHAR(1000) | NULL | URL request |
| ip_address | VARCHAR(45) | NULL | IP address |
| user_agent | VARCHAR(500) | NULL | User Agent |
| created_at | TIMESTAMP | NULL | — |

**Indeks:** `user_id`, `auditable_type`, `auditable_id`, `event`, `created_at`

**Catatan migrasi:** File aktif adalah `2026_06_04_000025_create_audit_trails_table.php`. File `2025_01_04_000001_create_audit_trails_table.php` adalah duplikat lama dan harus dihapus.

---

#### H3. `activity_logs`
Log aktivitas pengguna (login, akses halaman, dll).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | **UUID** |
| user_id | CHAR(36) | NULL, FK users.id | — |
| description | TEXT | NOT NULL | Deskripsi aktivitas |
| subject_type | VARCHAR(255) | NULL | Polymorphic: subject |
| subject_id | CHAR(36) | NULL | **UUID** |
| causer_type | VARCHAR(255) | NULL | Polymorphic: pelaku |
| causer_id | CHAR(36) | NULL | **UUID** |
| properties | JSON | NULL | Data tambahan |
| ip_address | VARCHAR(45) | NULL | — |
| created_at | TIMESTAMP | NULL | — |

---

#### H4. `jobs` (Laravel Queue)
Antrian pekerjaan background.

> Tabel standar Laravel — generated via `php artisan queue:table`. Tidak dimodifikasi.

---

#### H5. `cache` & `sessions`
Tabel bawaan Laravel untuk cache dan session.

> Tabel standar Laravel — tidak dimodifikasi.

---

## INVENTORY MIGRATION FILES

### Status Migration Aktif

| No | File Migration | Tabel | Status |
|----|---------------|-------|--------|
| 1 | `0001_01_01_000001_create_cache_table.php` | `cache`, `cache_locks` | ✅ Aktif (Laravel default) |
| 2 | `0001_01_01_000002_create_jobs_table.php` | `jobs`, `job_batches`, `failed_jobs` | ✅ Aktif (Laravel default) |
| 3 | `2026_06_04_000001_create_users_table.php` | `users` | ✅ Aktif — UUID CHAR(36) |
| 4 | `2026_06_04_000002_create_personal_access_tokens_table.php` | `personal_access_tokens` | ✅ Aktif |
| 5 | `2026_06_04_000003_create_otp_verifications_table.php` | `otp_verifications` | ✅ Aktif — UUID CHAR(36) |
| 6 | `2026_06_04_000004_create_faculties_table.php` | `faculties` | ✅ Aktif — UUID CHAR(36) |
| 7 | `2026_06_04_000005_create_study_programs_table.php` | `study_programs` | ✅ Aktif — UUID CHAR(36) |
| 8 | `2026_06_04_000006_create_profession_categories_table.php` | `profession_categories` | ✅ Aktif — UUID CHAR(36) |
| 9 | `2026_06_04_000007_create_professions_table.php` | `professions` | ✅ Aktif — UUID CHAR(36) |
| 10 | `2026_06_04_000008_create_institutions_table.php` | `institutions` | ✅ Aktif — UUID CHAR(36) |
| 11 | `2026_06_04_000009_create_institution_details_table.php` | `institution_details` | ✅ Aktif — UUID CHAR(36) |
| 12 | `2026_06_04_000010_create_alumni_table.php` | `alumni` | ✅ Aktif — UUID CHAR(36) |
| 13 | `2026_06_04_000011_create_alumni_employment_histories_table.php` | `alumni_employment_histories` | ✅ Aktif — UUID CHAR(36) |
| 14 | `2026_06_04_000012_create_alumni_requests_table.php` | `alumni_requests` | ✅ Aktif — UUID CHAR(36) |
| 15 | `2026_06_04_000013_create_employer_access_tokens_table.php` | `employer_access_tokens` | ✅ Aktif — UUID CHAR(36) |
| 16 | `2026_06_04_000018_create_tracer_studies_table.php` | `tracer_studies` | ✅ Aktif — UUID CHAR(36) |
| 17 | `2026_06_04_000019_add_tracer_study_fk_to_employer_access_tokens.php` | FK pada `employer_access_tokens` | ✅ Aktif |
| 18 | `2026_06_04_000020_create_tracer_study_questionnaires_table.php` | `tracer_study_questionnaires` | ✅ Aktif — UUID CHAR(36) |
| 19 | `2026_06_04_000021_create_questionnaire_responses_table.php` | `questionnaire_responses` | ✅ Aktif — UUID CHAR(36) |
| 20 | `2026_06_04_000022_create_questionnaire_answers_table.php` | `questionnaire_answers` | ✅ Aktif — UUID CHAR(36) |
| 21 | `2026_06_04_000023_create_notifications_table.php` | `notifications` | ✅ Aktif |
| 22 | `2026_06_04_000024_create_notification_logs_table.php` | `notification_logs` | ✅ Aktif — UUID CHAR(36) |
| 23 | `2026_06_04_000025_create_app_settings_table.php` | `app_settings` | ✅ Aktif — UUID CHAR(36) |
| 24 | `2026_06_04_000026_create_audit_trails_table.php` | `audit_trails` | ✅ Aktif — UUID CHAR(36) |
| 25 | `2026_06_04_000027_create_activity_logs_table.php` | `activity_logs` | ✅ Aktif — UUID CHAR(36) |
| 26 | `2026_06_05_000001_fix_personal_access_tokens_tokenable_id_to_char36.php` | Alter `personal_access_tokens` | ✅ Aktif |
| 27 | `2026_06_05_075238_create_sessions_table.php` | `sessions` | ✅ Aktif |
| 28 | `2026_06_06_000001_create_questionnaire_categories_table.php` | `questionnaire_categories` | ⚠️ **PERLU KOREKSI → UUID CHAR(36)** |
| 29 | `2026_06_06_000002_create_answer_types_table.php` | `answer_types` | ⚠️ **PERLU KOREKSI → UUID CHAR(36)** |
| 30 | `2026_06_06_000003_create_questionnaires_table.php` | `questionnaires` | ⚠️ **PERLU KOREKSI → UUID CHAR(36)** |
| 31 | `2026_06_06_000004_create_questionnaire_questions_table.php` | `questionnaire_questions` | ⚠️ **PERLU KOREKSI → UUID CHAR(36)** |

### Migration yang HARUS DIHAPUS (Duplikat/Obsolete)

| File | Alasan |
|------|--------|
| `2025_01_03_000001_create_faculties_table.php` | Duplikat — digantikan oleh `2026_06_04_000004` |
| `2025_01_03_000002_create_study_programs_table.php` | Duplikat — digantikan oleh `2026_06_04_000005` |
| `2025_01_04_000001_create_audit_trails_table.php` | Duplikat — digantikan oleh `2026_06_04_000026` |
| `2026_06_04_000014_create_questionnaire_categories_table.php` | Skeleton lama — digantikan oleh `2026_06_06_000001` |
| `2026_06_04_000015_create_answer_types_table.php` | Skeleton lama — digantikan oleh `2026_06_06_000002` |
| `2026_06_04_000016_create_questionnaires_table.php` | Skeleton lama — digantikan oleh `2026_06_06_000003` |
| `2026_06_04_000017_create_questionnaire_questions_table.php` | Skeleton lama — digantikan oleh `2026_06_06_000004` |

---

## RINGKASAN RELASI ANTAR TABEL

```
users ─────────────── alumni (1:1 user_id)
faculties ────────── study_programs (1:N)
study_programs ───── alumni (1:N)
profession_categories ─── professions (1:N)
institutions ─────── institution_details (1:1)
institutions ─────── alumni_employment_histories (1:N)
professions ──────── alumni_employment_histories (1:N)
alumni ───────────── alumni_employment_histories (1:N)
alumni ───────────── alumni_requests (1:N)
alumni ───────────── employer_access_tokens (1:N)
institutions ─────── employer_access_tokens (1:N)
questionnaire_categories ─── questionnaires (1:N)
questionnaires ───── questionnaire_questions (1:N)
answer_types ─────── questionnaire_questions (1:N)
tracer_studies ───── tracer_study_questionnaires (N:N via pivot)
questionnaires ───── tracer_study_questionnaires (N:N via pivot)
tracer_studies ───── questionnaire_responses (1:N)
questionnaires ───── questionnaire_responses (1:N)
alumni ───────────── questionnaire_responses (1:N)
employer_access_tokens ── questionnaire_responses (1:N)
questionnaire_responses ─ questionnaire_answers (1:N)
```

---

*Dokumen ini menjadi referensi migration database. Setiap perubahan skema harus dicatat di 09_CHANGELOG.md.*
*Terakhir diperbarui: 2026-06-06 oleh Software Architect — revert ke UUID CHAR(36), selaras dengan implementasi aktual Phase 1–3.*
