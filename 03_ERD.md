# 03_ERD.md — Entity Relationship Diagram Tracer Study UNISYA

**Versi:** 1.0.0
**Tanggal Dibuat:** 2026-06-04

---

## ERD TEKSTUAL (Crow's Foot Notation)

```
┌──────────────────────────────────────────────────────────────────────────────────┐
│                        TRACER STUDY UNISYA — ERD                                 │
└──────────────────────────────────────────────────────────────────────────────────┘

GROUP A: AUTENTIKASI & PENGGUNA
─────────────────────────────────────────────────────────────
[users] ─────────────────────────────── [personal_access_tokens]
  id (PK)                                 id (PK)
  name                                    tokenable_type
  email (UQ)                              tokenable_id ──→ users.id
  password                                name
  phone                                   token (UQ)
  role: super_admin|alumni                abilities
  is_active                               expires_at

[users] ─────────── 1 ──── N ─────────── [otp_verifications]
                                          id (PK)
                                          identifier (email/phone)
                                          identifier_type
                                          otp_code (hashed)
                                          purpose
                                          attempts / max_attempts
                                          expires_at

[users] ──── 1 ──── 1 ───────────────── [alumni]
  ↑                                       id (PK)
  └─ created_by/updated_by/deleted_by     user_id (FK→users, UNIQUE)
     (semua tabel punya audit fields)     nim (UQ)
                                          name
                                          gender
                                          birth_date

GROUP B: AKADEMIK
─────────────────────────────────────────────────────────────
[faculties] ─── 1 ──── N ─────────────── [study_programs]
  id (PK)                                  id (PK)
  code (UQ)                                faculty_id (FK)
  name                                     code (UQ)
                                           name
                                           degree_level

[study_programs] ─── 1 ──── N ─────────── [alumni]
                                           study_program_id (FK)

GROUP C: PROFESI & INSTITUSI
─────────────────────────────────────────────────────────────
[profession_categories] ─ 1 ─ N ────────── [professions]
  id (PK)                                    id (PK)
  name                                       profession_category_id (FK)
                                             name

[institutions] ─── 1 ──── 1 ──────────── [institution_details]
  id (PK)                                  institution_id (FK, UQ)
  name                                     address, city, province
  type                                     phone, email
  sector                                   contact_person

GROUP D: ALUMNI & PEKERJAAN
─────────────────────────────────────────────────────────────
[alumni] ──── 1 ──── N ────────────────── [alumni_employment_histories]
                                           id (PK)
                                           alumni_id (FK)
                                           institution_id (FK→institutions)
                                           profession_id (FK→professions)
                                           start_date / end_date
                                           is_current
                                           job_relevance

[alumni] ──── 1 ──── N ────────────────── [alumni_requests]
                                           id (PK)
                                           alumni_id (FK)
                                           type, field_name
                                           old_value, new_value
                                           status: menunggu|disetujui|ditolak

[alumni] ──── 1 ──── N ────────────────── [employer_access_tokens]
                                           id (PK)
                                           alumni_id (FK)
                                           institution_id (FK→institutions)
                                           contact_name, contact_phone
                                           token (UQ, hashed)
                                           is_used, is_revoked
                                           expires_at

GROUP E: MESIN KUESIONER
─────────────────────────────────────────────────────────────
[questionnaire_categories] ─ 1 ─ N ─────── [questionnaires]
  id (PK)                                    id (PK)
  name                                       questionnaire_category_id (FK)
                                             title
                                             respondent_type: alumni|employer|both
                                             scope: global|faculty|study_program
                                             faculty_id (FK, nullable)
                                             study_program_id (FK, nullable)
                                             version

[answer_types] ─── 1 ──── N ─────────── [questionnaire_questions]
  id (PK)                                 id (PK)
  code (UQ)                               questionnaire_id (FK)
  name                                    answer_type_id (FK)
  config (JSON)                           question_text
                                          question_order
                                          is_required

GROUP F: TRACER STUDY & RESPONS
─────────────────────────────────────────────────────────────
[tracer_studies] ─── 1 ──── N ────────── [tracer_study_questionnaires]
  id (PK)                                  tracer_study_id (FK)
  title                                    questionnaire_id (FK)
  academic_year                            ← PIVOT TABLE
  start_date / end_date
  status: draft|aktif|selesai|dibatalkan
  target_scope

[questionnaires] ─── 1 ──── N ──────────── [tracer_study_questionnaires]
                              ↑ many-to-many via pivot

[tracer_studies] ──── 1 ──── N ──────────── [questionnaire_responses]
[questionnaires] ──── 1 ──── N ─────────────────↑
[alumni]         ──── 1 ──── N ─────────────────↑
[employer_access_tokens] 1 ─ N ─────────────────↑
                                                  id (PK)
                                                  tracer_study_id (FK)
                                                  questionnaire_id (FK)
                                                  respondent_type
                                                  alumni_id (FK, nullable)
                                                  employer_access_token_id (FK, nullable)
                                                  submitted_at
                                                  questionnaire_snapshot (JSON) ← IMMUTABLE

[questionnaire_responses] ─── 1 ──── N ──── [questionnaire_answers]
                                              id (PK)
                                              questionnaire_response_id (FK)
                                              question_snapshot (JSON) ← IMMUTABLE
                                              answer_type_snapshot (JSON) ← IMMUTABLE
                                              answer_value

GROUP G: NOTIFIKASI
─────────────────────────────────────────────────────────────
[notifications] — polymorphic notifiable
[notification_logs] — log WA & Email dengan status

GROUP H: SISTEM
─────────────────────────────────────────────────────────────
[app_settings] — konfigurasi key-value per group
[audit_trails] — log CRUD semua model penting
[activity_logs] — log aktivitas pengguna
[jobs] — Laravel Queue jobs
[failed_jobs] — Laravel Queue failed jobs
```

---

## RELASI UTAMA (RINGKASAN)

| Dari | Ke | Tipe | Via |
|------|-----|------|-----|
| users | alumni | 1:1 | user_id |
| faculties | study_programs | 1:N | faculty_id |
| study_programs | alumni | 1:N | study_program_id |
| profession_categories | professions | 1:N | profession_category_id |
| institutions | institution_details | 1:1 | institution_id |
| institutions | alumni_employment_histories | 1:N | institution_id |
| professions | alumni_employment_histories | 1:N | profession_id |
| alumni | alumni_employment_histories | 1:N | alumni_id |
| alumni | alumni_requests | 1:N | alumni_id |
| alumni | employer_access_tokens | 1:N | alumni_id |
| institutions | employer_access_tokens | 1:N | institution_id |
| questionnaire_categories | questionnaires | 1:N | questionnaire_category_id |
| answer_types | questionnaire_questions | 1:N | answer_type_id |
| questionnaires | questionnaire_questions | 1:N | questionnaire_id |
| tracer_studies | questionnaires | N:M | tracer_study_questionnaires |
| tracer_studies | questionnaire_responses | 1:N | tracer_study_id |
| questionnaires | questionnaire_responses | 1:N | questionnaire_id |
| alumni | questionnaire_responses | 1:N | alumni_id |
| employer_access_tokens | questionnaire_responses | 1:N | employer_access_token_id |
| questionnaire_responses | questionnaire_answers | 1:N | questionnaire_response_id |

---

## CATATAN DESAIN PENTING

### Immutable Snapshot Pattern
Tabel `questionnaire_responses` menyimpan `questionnaire_snapshot` (JSON) yang berisi salinan lengkap struktur kuesioner pada saat respons dikirim. Tabel `questionnaire_answers` menyimpan `question_snapshot` dan `answer_type_snapshot`. Ini memastikan bahwa perubahan kuesioner di masa depan tidak mengubah data historis.

### Soft Delete Strategy
Semua tabel data master menggunakan `deleted_at` (Laravel SoftDeletes). Query secara default mengecualikan soft-deleted records. Admin dapat melihat dan memulihkan data yang di-soft-delete.

### UUID Strategy
Semua PK menggunakan UUID (CHAR 36) untuk mencegah enumerasi ID dan mendukung distribusi data di masa depan.

### Audit Field Strategy
Setiap tabel data bisnis menyimpan `created_by`, `updated_by`, `deleted_by` (FK ke users.id) untuk kelengkapan audit trail.
