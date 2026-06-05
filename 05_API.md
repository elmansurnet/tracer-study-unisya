# 05_API.md — Struktur API Tracer Study UNISYA

**Versi:** 1.0.0
**Tanggal Dibuat:** 2026-06-04
**Base URL:** `/api/v1`
**Auth:** Bearer Token (Sanctum)
**Format:** JSON (Content-Type: application/json)

---

## KONVENSI RESPONSE

### Sukses
```json
{
  "status": true,
  "message": "Berhasil",
  "data": { ... },
  "meta": { "current_page": 1, "per_page": 15, "total": 100 }
}
```

### Gagal / Error
```json
{
  "status": false,
  "message": "Pesan error",
  "errors": { "field": ["Validasi gagal"] }
}
```

### HTTP Status Codes
| Kode | Keterangan |
|------|------------|
| 200 | Sukses |
| 201 | Dibuat |
| 204 | Sukses tanpa konten |
| 400 | Request tidak valid |
| 401 | Tidak terautentikasi |
| 403 | Tidak diizinkan |
| 404 | Tidak ditemukan |
| 422 | Validasi gagal |
| 429 | Rate limit terlampaui |
| 500 | Server error |

---

## GROUP 1: AUTENTIKASI

### 1.1 Login (Email & Password)
```
POST /api/v1/auth/login
Body: { email, password }
Response: { token, user, alumni? }
Rate Limit: 5/menit per IP
```

### 1.2 Request OTP Login
```
POST /api/v1/auth/otp/request
Body: { identifier, identifier_type: "email"|"whatsapp" }
Response: { message, expires_in: 300 }
Rate Limit: 3/menit per identifier
```

### 1.3 Verifikasi OTP Login
```
POST /api/v1/auth/otp/verify
Body: { identifier, otp_code }
Response: { token, user, alumni? }
Rate Limit: 5/menit per identifier
```

### 1.4 Logout
```
POST /api/v1/auth/logout
Auth: Bearer Token
Response: { message }
```

### 1.5 Get Authenticated User
```
GET /api/v1/auth/me
Auth: Bearer Token
Response: { user, alumni?, permissions }
```

### 1.6 Request Employer OTP
```
POST /api/v1/employer/otp/request
Body: { token }  (employer_access_token plain)
Response: { message, contact_masked }
Rate Limit: 3/menit per token
```

### 1.7 Verifikasi Employer OTP
```
POST /api/v1/employer/otp/verify
Body: { token, otp_code }
Response: { access_token, employer_info, questionnaires }
```

---

## GROUP 2: MANAJEMEN PENGGUNA (Super Admin)

### 2.1 Daftar Pengguna
```
GET /api/v1/admin/users?page=1&per_page=15&search=&role=&is_active=
Auth: Super Admin
Response: { data: [users], meta }
```

### 2.2 Detail Pengguna
```
GET /api/v1/admin/users/{id}
Auth: Super Admin
```

### 2.3 Buat Pengguna
```
POST /api/v1/admin/users
Body: { name, email, password, phone, role, is_active }
Auth: Super Admin
```

### 2.4 Perbarui Pengguna
```
PUT /api/v1/admin/users/{id}
Body: { name, email, phone, role, is_active }
Auth: Super Admin
```

### 2.5 Hapus Pengguna (Soft Delete)
```
DELETE /api/v1/admin/users/{id}
Auth: Super Admin
```

### 2.6 Reset Password
```
POST /api/v1/admin/users/{id}/reset-password
Body: { new_password }
Auth: Super Admin
```

---

## GROUP 3: FAKULTAS

```
GET    /api/v1/admin/faculties          — Daftar (paginated + search)
POST   /api/v1/admin/faculties          — Buat baru
GET    /api/v1/admin/faculties/{id}     — Detail
PUT    /api/v1/admin/faculties/{id}     — Perbarui
DELETE /api/v1/admin/faculties/{id}     — Hapus (soft delete)
GET    /api/v1/faculties/list           — Dropdown (auth any)
```

## GROUP 4: PROGRAM STUDI

```
GET    /api/v1/admin/study-programs           — Daftar
POST   /api/v1/admin/study-programs           — Buat
GET    /api/v1/admin/study-programs/{id}      — Detail
PUT    /api/v1/admin/study-programs/{id}      — Perbarui
DELETE /api/v1/admin/study-programs/{id}      — Hapus
GET    /api/v1/study-programs/list            — Dropdown
GET    /api/v1/faculties/{id}/study-programs  — Per Fakultas
```

## GROUP 5: KATEGORI PROFESI & PROFESI

```
GET    /api/v1/admin/profession-categories
POST   /api/v1/admin/profession-categories
GET    /api/v1/admin/profession-categories/{id}
PUT    /api/v1/admin/profession-categories/{id}
DELETE /api/v1/admin/profession-categories/{id}

GET    /api/v1/admin/professions
POST   /api/v1/admin/professions
GET    /api/v1/admin/professions/{id}
PUT    /api/v1/admin/professions/{id}
DELETE /api/v1/admin/professions/{id}
```

## GROUP 6: INSTITUSI & DETAIL INSTITUSI

```
GET    /api/v1/admin/institutions
POST   /api/v1/admin/institutions
GET    /api/v1/admin/institutions/{id}
PUT    /api/v1/admin/institutions/{id}
DELETE /api/v1/admin/institutions/{id}

GET    /api/v1/admin/institutions/{id}/detail
PUT    /api/v1/admin/institutions/{id}/detail
POST   /api/v1/admin/institutions/{id}/detail

GET    /api/v1/institutions/list    — Dropdown (auth alumni)
```

## GROUP 7: ALUMNI (Super Admin)

```
GET    /api/v1/admin/alumni                    — Daftar + filter
POST   /api/v1/admin/alumni                    — Buat manual
GET    /api/v1/admin/alumni/{id}               — Detail
PUT    /api/v1/admin/alumni/{id}               — Perbarui
DELETE /api/v1/admin/alumni/{id}               — Hapus

POST   /api/v1/admin/alumni/import             — Import Excel
GET    /api/v1/admin/alumni/export/excel       — Export Excel
GET    /api/v1/admin/alumni/export/pdf         — Export PDF

GET    /api/v1/admin/alumni/{id}/employment    — Riwayat pekerjaan
POST   /api/v1/admin/alumni/{id}/employment    — Tambah riwayat
```

## GROUP 8: ALUMNI (Self Management)

```
GET    /api/v1/alumni/profile            — Profil sendiri
PUT    /api/v1/alumni/profile            — Update profil
PUT    /api/v1/alumni/change-password    — Ganti password

GET    /api/v1/alumni/employment         — Riwayat pekerjaan
POST   /api/v1/alumni/employment         — Tambah pekerjaan
PUT    /api/v1/alumni/employment/{id}    — Update pekerjaan
DELETE /api/v1/alumni/employment/{id}    — Hapus pekerjaan

GET    /api/v1/alumni/requests           — Daftar permohonan
POST   /api/v1/alumni/requests           — Ajukan permohonan

GET    /api/v1/alumni/employer-tokens              — Daftar token employer
POST   /api/v1/alumni/employer-tokens              — Buat token undangan
DELETE /api/v1/alumni/employer-tokens/{id}         — Cabut token
```

## GROUP 9: PERMOHONAN ALUMNI (Admin)

```
GET    /api/v1/admin/alumni-requests              — Daftar (filter status)
GET    /api/v1/admin/alumni-requests/{id}         — Detail
PUT    /api/v1/admin/alumni-requests/{id}/approve — Setujui
PUT    /api/v1/admin/alumni-requests/{id}/reject  — Tolak
```

## GROUP 10: KUESIONER

```
GET    /api/v1/admin/questionnaire-categories
POST   /api/v1/admin/questionnaire-categories
PUT    /api/v1/admin/questionnaire-categories/{id}
DELETE /api/v1/admin/questionnaire-categories/{id}

GET    /api/v1/admin/answer-types
POST   /api/v1/admin/answer-types
PUT    /api/v1/admin/answer-types/{id}
DELETE /api/v1/admin/answer-types/{id}

GET    /api/v1/admin/questionnaires
POST   /api/v1/admin/questionnaires
GET    /api/v1/admin/questionnaires/{id}
PUT    /api/v1/admin/questionnaires/{id}
DELETE /api/v1/admin/questionnaires/{id}

GET    /api/v1/admin/questionnaires/{id}/questions      — Daftar pertanyaan
POST   /api/v1/admin/questionnaires/{id}/questions      — Tambah pertanyaan
PUT    /api/v1/admin/questionnaires/{id}/questions/{qid} — Update pertanyaan
DELETE /api/v1/admin/questionnaires/{id}/questions/{qid} — Hapus pertanyaan
POST   /api/v1/admin/questionnaires/{id}/questions/reorder — Ubah urutan
```

## GROUP 11: TRACER STUDY

```
GET    /api/v1/admin/tracer-studies
POST   /api/v1/admin/tracer-studies
GET    /api/v1/admin/tracer-studies/{id}
PUT    /api/v1/admin/tracer-studies/{id}
DELETE /api/v1/admin/tracer-studies/{id}
POST   /api/v1/admin/tracer-studies/{id}/activate    — Aktifkan & kirim undangan
POST   /api/v1/admin/tracer-studies/{id}/complete    — Tandai selesai
GET    /api/v1/admin/tracer-studies/{id}/responses   — Lihat respons
GET    /api/v1/admin/tracer-studies/{id}/stats       — Statistik tingkat respons
```

## GROUP 12: PENGISIAN KUESIONER

```
GET    /api/v1/alumni/tracer-studies              — Daftar TS yang dapat diisi
GET    /api/v1/alumni/tracer-studies/{id}         — Detail TS + kuesioner
POST   /api/v1/alumni/questionnaires/{id}/submit  — Submit jawaban
Body: { tracer_study_id, answers: [{ question_id, answer_value }] }

GET    /api/v1/employer/questionnaires            — Daftar kuesioner employer
POST   /api/v1/employer/questionnaires/{id}/submit — Submit jawaban employer
Auth: Sanctum token dengan ability 'employer'
```

## GROUP 13: PELAPORAN

```
GET /api/v1/admin/reports/dashboard           — KPI Dashboard
GET /api/v1/admin/reports/alumni-distribution — Distribusi alumni
GET /api/v1/admin/reports/faculty-analytics   — Per Fakultas
GET /api/v1/admin/reports/program-analytics   — Per Program Studi
GET /api/v1/admin/reports/employment-analytics — Analitik pekerjaan
GET /api/v1/admin/reports/waiting-period      — Masa tunggu
GET /api/v1/admin/reports/employer-analytics  — Analitik employer
GET /api/v1/admin/reports/questionnaire/{id}  — Hasil kuesioner spesifik

Query params (semua): ?start_date=&end_date=&faculty_id=&study_program_id=

GET /api/v1/admin/reports/export/excel?type=alumni&...
GET /api/v1/admin/reports/export/pdf?type=alumni&paper=a4&...
```

## GROUP 14: PENGATURAN SISTEM

```
GET    /api/v1/admin/settings              — Semua setting per group
GET    /api/v1/admin/settings/{group}      — Setting per group
PUT    /api/v1/admin/settings              — Bulk update
POST   /api/v1/admin/settings/test-wa      — Test WA Gateway
POST   /api/v1/admin/settings/test-smtp    — Test SMTP
```

## GROUP 15: AUDIT & ACTIVITY LOG

```
GET /api/v1/admin/audit-trails?model=&event=&user_id=&start_date=&end_date=
GET /api/v1/admin/activity-logs?user_id=&start_date=&end_date=
```

---

## RATE LIMITING

| Endpoint Group | Limit | Window |
|----------------|-------|--------|
| Auth login | 5 request | 1 menit |
| OTP request | 3 request | 1 menit |
| OTP verify | 5 request | 5 menit |
| API umum (auth) | 60 request | 1 menit |
| Export | 5 request | 1 menit |
| Import | 3 request | 5 menit |
