# 02_DATABASE.md — Desain Database Tracer Study UNISYA

**Versi:** 1.0.2
**Tanggal Dibuat:** 2026-06-05
**Database Engine:** MySQL 8.0+
**Charset:** utf8mb4
**Collation:** utf8mb4_unicode_ci

---

### Revisi minor v1.0.2

1. Pada tabel `users`, pertahankan enum role final:
   - `'super_admin', 'alumni'`

2. Pada tabel `employer_access_tokens`, tambahkan catatan implementasi:
   - `tracer_study_id` boleh dibuat nullable pada migration utama.
   - Foreign key `tracer_study_id -> tracer_studies.id` dapat dipasang melalui migration tambahan terpisah untuk menghindari circular dependency urutan migrasi.

3. Istilah aktor:
   - Gunakan istilah **Employer (Pengguna Alumni)** secara konsisten di seluruh dokumen.
   - Hindari pencampuran istilah `Pengguna Alumni` sebagai nama role teknis database; role teknis tetap hanya `super_admin` dan `alumni`.

---

## KONVENSI PENAMAAN

- Nama tabel: `snake_case`, bentuk jamak
- Nama kolom: `snake_case`
- Primary Key: `id` (UUID v7 / ULID, CHAR(36))
- Foreign Key: `{tabel_referensi_singular}_id` (CHAR(36))
- Audit fields wajib: `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`
- Normalisasi minimal: 3NF

---

## DAFTAR TABEL

### GROUP A: AUTENTIKASI & PENGGUNA

---

#### A1. `users` (UPDATED ENUM ROLE)
Menyimpan akun pengguna sistem (Super Admin dan Alumni).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK, NOT NULL | UUID |
| name | VARCHAR(255) | NOT NULL | Nama lengkap |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Email login |
| email_verified_at | TIMESTAMP | NULL | Waktu verifikasi email |
| password | VARCHAR(255) | NOT NULL | Hash bcrypt |
| phone | VARCHAR(20) | NULL | Nomor WhatsApp |
| phone_verified_at | TIMESTAMP | NULL | Waktu verifikasi WA |
| role | ENUM | NOT NULL | 'super_admin', 'alumni' |
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

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | BIGINT UNSIGNED | PK AUTO_INCREMENT | — |
| tokenable_type | VARCHAR(255) | NOT NULL | Polymorphic type |
| tokenable_id | CHAR(36) | NOT NULL | Polymorphic ID |
| name | VARCHAR(255) | NOT NULL | Nama token |
| token | VARCHAR(64) | UNIQUE NOT NULL | Hash token |
| abilities | TEXT | NULL | JSON abilities |
| last_used_at | TIMESTAMP | NULL | — |
| expires_at | TIMESTAMP | NULL | — |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |

---

#### A3. `otp_verifications`
Menyimpan OTP untuk verifikasi alumni dan employer.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | UUID |
| identifier | VARCHAR(255) | NOT NULL | Email atau nomor HP |
| identifier_type | ENUM | NOT NULL | 'email', 'whatsapp' |
| otp_code | VARCHAR(10) | NOT NULL | Kode OTP (hashed) |
| purpose | ENUM | NOT NULL | 'login', 'employer_access', 'phone_verify', 'email_verify' |
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
Token akses khusus untuk Pengguna Alumni (employer) — tanpa registrasi.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | UUID |
| alumni_id | CHAR(36) | FK alumni.id, NOT NULL | Alumni yang mengundang |
| institution_id | CHAR(36) | FK institutions.id, NOT NULL | Institusi employer |
| contact_name | VARCHAR(255) | NOT NULL | Nama kontak employer |
| contact_phone | VARCHAR(20) | NULL | Nomor WA employer |
| contact_email | VARCHAR(255) | NULL | Email employer |
| token | VARCHAR(64) | UNIQUE, NOT NULL | Token unik (hashed) |
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

---

### GROUP B: DATA AKADEMIK

---

#### B1. `faculties`
Data Fakultas UNISYA.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | UUID |
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

---

#### B2. `study_programs`
Data Program Studi UNISYA.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | UUID |
| faculty_id | CHAR(36) | FK faculties.id, NOT NULL | Fakultas induk |
| code | VARCHAR(20) | UNIQUE, NOT NULL | Kode program studi |
| name | VARCHAR(255) | NOT NULL | Nama program studi |
| degree_level | ENUM | NOT NULL | 'D3', 'S1', 'S2', 'S3', 'Profesi' |
| description | TEXT | NULL | Deskripsi |
| is_active | TINYINT(1) | DEFAULT 1 | Status aktif |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

---

### GROUP C: DATA PROFESI & INSTITUSI

---

#### C1. `profession_categories`
Kategori profesi/bidang pekerjaan.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | UUID |
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
| id | CHAR(36) | PK | UUID |
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
| id | CHAR(36) | PK | UUID |
| name | VARCHAR(255) | NOT NULL | Nama institusi |
| type | ENUM | NOT NULL | 'pemerintah', 'swasta', 'bumn', 'pendidikan', 'lainnya' |
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
| id | CHAR(36) | PK | UUID |
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
| id | CHAR(36) | PK | UUID |
| user_id | CHAR(36) | FK users.id, UNIQUE NULL | Akun user (nullable jika belum punya akun) |
| study_program_id | CHAR(36) | FK study_programs.id, NOT NULL | Program studi |
| nim | VARCHAR(50) | UNIQUE, NOT NULL | Nomor Induk Mahasiswa |
| name | VARCHAR(255) | NOT NULL | Nama lengkap |
| gender | ENUM | NOT NULL | 'laki_laki', 'perempuan' |
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
| employment_status | ENUM | NULL | 'bekerja', 'wirausaha', 'melanjutkan_studi', 'belum_bekerja' |
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
| id | CHAR(36) | PK | UUID |
| alumni_id | CHAR(36) | FK alumni.id, NOT NULL | Alumni terkait |
| institution_id | CHAR(36) | FK institutions.id, NULL | Institusi tempat bekerja |
| profession_id | CHAR(36) | FK professions.id, NULL | Profesi/jabatan |
| job_title | VARCHAR(255) | NULL | Judul jabatan spesifik |
| start_date | DATE | NOT NULL | Tanggal mulai bekerja |
| end_date | DATE | NULL | Tanggal berakhir (null = saat ini) |
| is_current | TINYINT(1) | DEFAULT 0 | Pekerjaan saat ini? |
| salary_range | ENUM | NULL | '<1jt','1-3jt','3-5jt','5-10jt','>10jt' |
| job_relevance | ENUM | NULL | 'sangat_relevan','relevan','kurang_relevan','tidak_relevan' |
| notes | TEXT | NULL | Catatan |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |
| deleted_at | TIMESTAMP | NULL | Soft delete |
| created_by | CHAR(36) | NULL, FK users.id | — |
| updated_by | CHAR(36) | NULL, FK users.id | — |
| deleted_by | CHAR(36) | NULL, FK users.id | — |

---

#### D3. `alumni_requests`
Permohonan update data akademik oleh alumni (Alumni Request Management).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | UUID |
| alumni_id | CHAR(36) | FK alumni.id, NOT NULL | Alumni pemohon |
| type | ENUM | NOT NULL | 'update_akademik', 'update_profil', 'lainnya' |
| field_name | VARCHAR(100) | NOT NULL | Field yang diminta diubah |
| old_value | TEXT | NULL | Nilai lama |
| new_value | TEXT | NOT NULL | Nilai baru yang diminta |
| reason | TEXT | NULL | Alasan permohonan |
| status | ENUM | DEFAULT 'menunggu' | 'menunggu', 'disetujui', 'ditolak' |
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
| id | CHAR(36) | PK | UUID |
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

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | UUID |
| code | VARCHAR(50) | UNIQUE, NOT NULL | 'true_false', 'scale_1_5', 'scale_1_10' |
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
| id | CHAR(36) | PK | UUID |
| questionnaire_category_id | CHAR(36) | FK questionnaire_categories.id, NOT NULL | Kategori |
| title | VARCHAR(255) | NOT NULL | Judul kuesioner |
| description | TEXT | NULL | Deskripsi |
| respondent_type | ENUM | NOT NULL | 'alumni', 'employer', 'both' |
| scope | ENUM | NOT NULL | 'global', 'faculty', 'study_program' |
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
| id | CHAR(36) | PK | UUID |
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
| id | CHAR(36) | PK | UUID |
| title | VARCHAR(255) | NOT NULL | Judul sesi |
| description | TEXT | NULL | — |
| academic_year | VARCHAR(20) | NOT NULL | Tahun akademik (2024/2025) |
| start_date | DATE | NOT NULL | Tanggal mulai |
| end_date | DATE | NOT NULL | Tanggal berakhir |
| status | ENUM | DEFAULT 'draft' | 'draft', 'aktif', 'selesai', 'dibatalkan' |
| target_scope | ENUM | DEFAULT 'all' | 'all', 'faculty', 'study_program' |
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

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | UUID |
| tracer_study_id | CHAR(36) | FK tracer_studies.id, NOT NULL | Sesi tracer |
| questionnaire_id | CHAR(36) | FK questionnaires.id, NOT NULL | Kuesioner |
| order | SMALLINT | DEFAULT 0 | Urutan tampil |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |

**UNIQUE:** (`tracer_study_id`, `questionnaire_id`)

---

#### F3. `questionnaire_responses`
Header respons kuesioner (satu baris per alumni/employer per kuesioner per sesi).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | UUID |
| tracer_study_id | CHAR(36) | FK tracer_studies.id, NULL | Sesi tracer (null = diluar sesi) |
| questionnaire_id | CHAR(36) | FK questionnaires.id, NOT NULL | Kuesioner yang diisi |
| respondent_type | ENUM | NOT NULL | 'alumni', 'employer' |
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
| id | CHAR(36) | PK | UUID |
| questionnaire_response_id | CHAR(36) | FK questionnaire_responses.id, NOT NULL | Header respons |
| question_snapshot | JSON | NOT NULL | **SNAPSHOT** teks pertanyaan saat diisi |
| answer_type_snapshot | JSON | NOT NULL | **SNAPSHOT** konfigurasi tipe jawaban |
| answer_value | VARCHAR(255) | NOT NULL | Nilai jawaban |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |

**Indeks:** `questionnaire_response_id`

---

### GROUP G: SISTEM NOTIFIKASI

---

#### G1. `notifications`
Log notifikasi yang dikirim.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | UUID |
| type | VARCHAR(255) | NOT NULL | Class notifikasi Laravel |
| notifiable_type | VARCHAR(255) | NOT NULL | Polymorphic type |
| notifiable_id | CHAR(36) | NOT NULL | Polymorphic ID |
| data | JSON | NOT NULL | Isi notifikasi |
| read_at | TIMESTAMP | NULL | Waktu dibaca |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |

---

#### G2. `notification_logs`
Log pengiriman WA dan Email untuk monitoring.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | UUID |
| channel | ENUM | NOT NULL | 'whatsapp', 'email' |
| recipient | VARCHAR(255) | NOT NULL | Nomor WA atau email |
| subject | VARCHAR(255) | NULL | Subjek (email) |
| message | TEXT | NOT NULL | Isi pesan |
| status | ENUM | DEFAULT 'pending' | 'pending', 'terkirim', 'gagal' |
| provider_response | JSON | NULL | Respons API WA/SMTP |
| error_message | TEXT | NULL | Pesan error jika gagal |
| retries | TINYINT | DEFAULT 0 | Jumlah percobaan ulang |
| sent_at | TIMESTAMP | NULL | Waktu terkirim |
| reference_type | VARCHAR(100) | NULL | Polymorphic: model terkait |
| reference_id | CHAR(36) | NULL | ID model terkait |
| created_at | TIMESTAMP | NULL | — |
| updated_at | TIMESTAMP | NULL | — |

---

### GROUP H: PENGATURAN & SISTEM

---

#### H1. `app_settings`
Konfigurasi sistem yang dapat diubah via UI.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | UUID |
| group | VARCHAR(100) | NOT NULL | Kelompok: 'general', 'wa_gateway', 'smtp', 'security' |
| key | VARCHAR(100) | NOT NULL | Kunci setting |
| value | TEXT | NULL | Nilai setting |
| type | ENUM | DEFAULT 'string' | 'string', 'boolean', 'integer', 'json', 'password' |
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
| id | CHAR(36) | PK | UUID |
| user_id | CHAR(36) | NULL, FK users.id | Pengguna yang melakukan aksi |
| user_type | ENUM | NULL | 'user', 'employer', 'system' |
| event | VARCHAR(50) | NOT NULL | 'created', 'updated', 'deleted', 'restored' |
| auditable_type | VARCHAR(255) | NOT NULL | Model yang diaudit |
| auditable_id | CHAR(36) | NOT NULL | ID record yang diaudit |
| old_values | JSON | NULL | Nilai sebelum perubahan |
| new_values | JSON | NULL | Nilai setelah perubahan |
| url | VARCHAR(1000) | NULL | URL request |
| ip_address | VARCHAR(45) | NULL | IP address |
| user_agent | VARCHAR(500) | NULL | User Agent |
| created_at | TIMESTAMP | NULL | — |

**Indeks:** `user_id`, `auditable_type`, `auditable_id`, `event`, `created_at`

---

#### H3. `activity_logs`
Log aktivitas pengguna (login, akses halaman, dll).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| id | CHAR(36) | PK | UUID |
| user_id | CHAR(36) | NULL, FK users.id | — |
| description | TEXT | NOT NULL | Deskripsi aktivitas |
| subject_type | VARCHAR(255) | NULL | Polymorphic: subject |
| subject_id | CHAR(36) | NULL | — |
| causer_type | VARCHAR(255) | NULL | Polymorphic: pelaku |
| causer_id | CHAR(36) | NULL | — |
| properties | JSON | NULL | Data tambahan |
| ip_address | VARCHAR(45) | NULL | — |
| created_at | TIMESTAMP | NULL | — |

---

#### H4. `jobs` (Laravel Queue)
Antrian pekerjaan background (standard Laravel).

Standard Laravel jobs table — generated via `php artisan queue:table`.

---

#### H5. `failed_jobs` (Laravel Queue)
Pekerjaan yang gagal diproses.

Standard Laravel failed_jobs table.

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
tracer_studies ───── tracer_study_questionnaires (1:N)
questionnaires ───── tracer_study_questionnaires (1:N)
tracer_studies ───── questionnaire_responses (1:N)
questionnaires ───── questionnaire_responses (1:N)
alumni ───────────── questionnaire_responses (1:N)
employer_access_tokens ── questionnaire_responses (1:N)
questionnaire_responses ─ questionnaire_answers (1:N)
```

---

*Dokumen ini menjadi referensi migration database. Setiap perubahan skema harus dicatat di 09_CHANGELOG.md.*