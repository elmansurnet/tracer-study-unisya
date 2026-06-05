# 06_UI_UX.md — Desain UI/UX Tracer Study UNISYA

**Versi:** 1.0.0
**Tanggal Dibuat:** 2026-06-04

---

## 1. PRINSIP DESAIN

- **Profesional:** Menggunakan Bahasa Indonesia formal dan terminologi akademik
- **Intuitif:** Navigasi hierarkis yang jelas untuk 3 tipe pengguna berbeda
- **Responsif:** Mobile-first, mendukung min 375px hingga 1920px+
- **Aksesibel:** WCAG 2.1 Level AA
- **Konsisten:** Design system terpusat dengan Tailwind CSS

---

## 2. TEMA & WARNA

### Palet Warna Utama
- **Primary:** Teal `#0d6c7c` — melambangkan kepercayaan akademik
- **Secondary:** Abu-abu netral `#475569`
- **Accent:** Kuning emas `#d97706` — identitas UNISYA (Islam)
- **Success:** Hijau `#059669`
- **Warning:** Oranye `#d97706`
- **Error:** Merah `#dc2626`
- **Background:** `#f8fafc`
- **Surface:** `#ffffff`
- **Dark Mode:** Didukung penuh

### Tipografi
- **Display/Heading:** Plus Jakarta Sans (Google Fonts)
- **Body:** Inter (Google Fonts)
- **Monospace:** JetBrains Mono (untuk kode/token)

---

## 3. LAYOUT SISTEM

### 3.1 Layout Admin (Super Admin)
```
┌──────────────────────────────────────────────────────────┐
│  HEADER: Logo UNISYA | Nama Halaman | Notif | Avatar     │
├────────────┬─────────────────────────────────────────────┤
│            │                                             │
│  SIDEBAR   │              MAIN CONTENT                   │
│  (240px)   │                                             │
│            │  ┌─ Breadcrumb ──────────────────────────┐  │
│  • Dashboard│  │                                       │  │
│  • Alumni  │  │  ┌─ Page Title + Actions ───────────┐  │  │
│  • Kuesioner│  │  │                                  │  │  │
│  • Tracer  │  │  └──────────────────────────────────┘  │  │
│  • Laporan │  │                                       │  │
│  • Master  │  │  ┌─ Content Area ──────────────────┐  │  │
│  • Pengaturan│  │  │  Table / Form / Chart / Cards  │  │  │
│  • Log     │  │  └────────────────────────────────┘  │  │
│            │  └───────────────────────────────────────┘  │
└────────────┴─────────────────────────────────────────────┘
```

### 3.2 Layout Alumni
```
┌──────────────────────────────────────────────────────────┐
│  HEADER: Logo | Nama Alumni | Notif | Avatar             │
├────────────┬─────────────────────────────────────────────┤
│  SIDEBAR   │              MAIN CONTENT                   │
│            │                                             │
│  • Dashboard│                                             │
│  • Profil  │                                             │
│  • Pekerjaan│                                             │
│  • Tracer  │                                             │
│  • Permohonan│                                            │
│  • Employer│                                             │
└────────────┴─────────────────────────────────────────────┘
```

### 3.3 Layout Employer (Minimal)
```
┌──────────────────────────────────────────────────────────┐
│  HEADER: Logo UNISYA | "Portal Pengguna Alumni"          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│         CENTERED CONTENT (max-width: 800px)              │
│                                                          │
│  ┌─ Selamat Datang / Kuesioner / Konfirmasi ───────────┐  │
│  │                                                      │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

---

## 4. HALAMAN & KOMPONEN

### 4.1 Halaman Auth
- `/login` — Form login email/password + tombol OTP WhatsApp + OTP Email
- `/login/otp` — Input OTP 6 digit dengan countdown timer
- `/employer/access` — Landing page employer (input token)
- `/employer/otp` — Verifikasi OTP employer

### 4.2 Dashboard Admin
**KPI Cards (row):**
- Total Alumni | Alumni Bekerja | Tingkat Respons Tracer | Sesi Aktif

**Charts (ApexCharts):**
- Donut: Status Pekerjaan Alumni (Bekerja/Wirausaha/Studi/Belum)
- Bar: Alumni per Fakultas
- Line: Tren Wisuda per Tahun
- Bar: Top 10 Institusi Employer
- Gauge: Tingkat Respons Tracer Study Aktif

### 4.3 Manajemen Alumni
- Tabel alumni (sortable, filterable, searchable, paginated)
- Filter: Fakultas, Program Studi, Tahun Lulus, Status Pekerjaan
- Aksi: Lihat Detail, Edit, Hapus, Import Excel, Export Excel/PDF
- Form Alumni: step-by-step (Data Pribadi → Data Akademik → Data Pekerjaan)

### 4.4 Mesin Kuesioner
- Builder kuesioner: tambah pertanyaan dengan drag-and-drop urutan
- Preview kuesioner real-time
- Tipe jawaban visual: Toggle (T/F), Slider Skala (1-5, 1-10)

### 4.5 Portal Pengisian Kuesioner (Alumni)
- Satu pertanyaan per layar (wizard style) atau semua sekaligus
- Progress bar pengisian
- Auto-save draft jawaban
- Konfirmasi sebelum submit final

### 4.6 Portal Employer
- Halaman sambutan dengan info alumni yang mengundang
- Pertanyaan kuesioner dalam format card
- Submit confirmation dengan nomor respons

### 4.7 Dashboard Laporan
- Filter global: rentang tanggal, fakultas, program studi
- Tab: Distribusi Alumni | Pekerjaan | Masa Tunggu | Employer | Kuesioner
- Tombol export per laporan (Excel/PDF dengan pilihan ukuran kertas A4/F4)

---

## 5. KOMPONEN REUSABLE (VUE)

### Base Components
| Komponen | Deskripsi |
|----------|-----------|
| `AppButton` | Primary/secondary/ghost/danger + loading state |
| `AppInput` | Text input dengan label, error, prefix/suffix |
| `AppSelect` | Dropdown dengan search |
| `AppTextarea` | Textarea dengan counter |
| `AppBadge` | Status badge berwarna |
| `AppModal` | Modal dengan slot header/body/footer |
| `AppDrawer` | Side drawer untuk form edit |
| `AppTable` | Tabel dengan sort, pagination, empty state |
| `AppPagination` | Navigasi halaman |
| `AppToast` | Notifikasi toast (success/error/warning/info) |
| `AppSkeleton` | Skeleton loader |
| `AppCard` | Card container |
| `AppAlert` | Alert inline |
| `AppConfirm` | Dialog konfirmasi hapus/aksi penting |
| `AppDatePicker` | Date range picker |

### Domain Components
| Komponen | Deskripsi |
|----------|-----------|
| `AlumniCard` | Kartu info alumni |
| `QuestionnaireBuilder` | Builder pertanyaan kuesioner |
| `QuestionCard` | Card pertanyaan dengan tipe jawaban |
| `AnswerScaleInput` | Slider input untuk skala |
| `AnswerTrueFalseInput` | Toggle input T/F |
| `TracerStudyStatusBadge` | Badge status tracer study |
| `OtpInput` | Input 6 digit OTP dengan auto-focus |
| `ExportButton` | Tombol export dengan pilihan format/ukuran |
| `ChartWidget` | Wrapper ApexCharts |
| `KpiCard` | Kartu KPI dengan ikon dan tren |

---

## 6. STATE MANAGEMENT (PINIA STORES)

| Store | State |
|-------|-------|
| `useAuthStore` | user, token, permissions, isLoggedIn |
| `useAlumniStore` | alumni list, filters, pagination, current alumni |
| `useTracerStudyStore` | sessions, active session |
| `useQuestionnaireStore` | questionnaires, questions, answer draft |
| `useReportStore` | report data, filters, loading states |
| `useNotificationStore` | unread count, notifications list |
| `useSettingStore` | app settings |
| `useUIStore` | sidebar state, theme, loading overlay |

---

## 7. ROUTING VUE ROUTER

### Admin Routes (prefix: /admin)
```
/admin/dashboard
/admin/pengguna
/admin/fakultas
/admin/program-studi
/admin/kategori-profesi
/admin/profesi
/admin/institusi
/admin/alumni
/admin/alumni/:id
/admin/permohonan-alumni
/admin/kategori-kuesioner
/admin/tipe-jawaban
/admin/kuesioner
/admin/kuesioner/:id/pertanyaan
/admin/tracer-study
/admin/tracer-study/:id
/admin/laporan
/admin/pengaturan
/admin/audit-trail
/admin/activity-log
```

### Alumni Routes (prefix: /alumni)
```
/alumni/dashboard
/alumni/profil
/alumni/pekerjaan
/alumni/permohonan
/alumni/employer
/alumni/tracer-study
/alumni/tracer-study/:id/isi
```

### Employer Routes (prefix: /employer)
```
/employer/akses          (landing + token input)
/employer/verifikasi-otp
/employer/dashboard
/employer/kuesioner/:id
/employer/selesai
```

### Public Routes
```
/login
/login/otp
```

---

## 8. VALIDASI FORM (UX)

- Validasi real-time (on blur) untuk field penting
- Error message dalam Bahasa Indonesia yang spesifik
- Disable tombol submit jika form tidak valid
- Loading state pada tombol saat proses berlangsung
- Konfirmasi dialog untuk aksi destruktif (hapus, tolak)
- Auto-format nomor telepon (format 62xxxxxxxxx)

---

## 9. NOTIFIKASI IN-APP

- Toast: sukses, error, peringatan, info (pojok kanan atas, auto-dismiss 4 detik)
- Badge notifikasi pada ikon lonceng di header
- Panel notifikasi slide-in dari kanan

---

## 10. AKSESIBILITAS

- Semua form input memiliki label yang benar
- Keyboard navigation support
- ARIA labels pada ikon button
- Focus visible ring
- Skip to content link
- Contrast ratio minimum 4.5:1 untuk teks normal