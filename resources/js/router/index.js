import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useUIStore } from '@/stores/ui'

const PlaceholderPage = () => import('@/pages/shared/PlaceholderPage.vue')

const routes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('@/pages/auth/LoginPage.vue'),
    meta: { guestOnly: true, layout: 'auth' },
  },
  {
    path: '/login/otp',
    name: 'login-otp',
    component: () => import('@/pages/auth/OtpPage.vue'),
    meta: { guestOnly: true, layout: 'auth' },
  },
  {
    path: '/employer/akses',
    name: 'employer-access',
    component: () => import('@/pages/employer/AccessPage.vue'),
    meta: { layout: 'employer-public' },
  },
  {
    path: '/employer/verifikasi-otp',
    name: 'employer-otp',
    component: () => import('@/pages/employer/OtpPage.vue'),
    meta: { layout: 'employer-public' },
  },
  {
    path: '/employer',
    component: () => import('@/layouts/EmployerLayout.vue'),
    children: [
      {
        path: 'dashboard',
        name: 'employer-dashboard',
        component: () => import('@/pages/employer/DashboardPage.vue'),
      },
      {
        path: 'kuesioner/:id',
        name: 'employer-questionnaire',
        component: PlaceholderPage,
        props: {
          title: 'Kuesioner Employer',
          breadcrumbs: [
            { label: 'Employer' },
            { label: 'Kuesioner' },
          ],
        },
      },
      {
        path: 'selesai',
        name: 'employer-finish',
        component: PlaceholderPage,
        props: {
          title: 'Konfirmasi Selesai',
          breadcrumbs: [
            { label: 'Employer' },
            { label: 'Selesai' },
          ],
        },
      },
    ],
  },
  {
    path: '/admin',
    component: () => import('@/layouts/AdminLayout.vue'),
    meta: { requiresAuth: true, role: 'super_admin' },
    children: [
      {
        path: 'dashboard',
        name: 'admin-dashboard',
        component: () => import('@/pages/admin/DashboardPage.vue'),
      },

      // Phase 2A — Master Data
      {
        path: 'pengguna',
        name: 'admin.users',
        component: () => import('@/pages/admin/users/UsersPage.vue'),
        meta: { title: 'Manajemen Pengguna' },
      },
      {
        path: 'fakultas',
        name: 'admin.faculties',
        component: () => import('@/pages/admin/faculties/FacultiesPage.vue'),
        meta: { title: 'Manajemen Fakultas' },
      },
      {
        path: 'program-studi',
        name: 'admin.study-programs',
        component: () => import('@/pages/admin/study-programs/StudyProgramsPage.vue'),
        meta: { title: 'Manajemen Program Studi' },
      },
      // End Phase 2A - Master Data

      {
        path: 'kategori-profesi',
        name: 'admin-profession-categories',
        component: PlaceholderPage,
        props: {
          title: 'Kategori Profesi',
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Kategori Profesi' },
          ],
        },
      },
      {
        path: 'profesi',
        name: 'admin-professions',
        component: PlaceholderPage,
        props: {
          title: 'Manajemen Profesi',
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Profesi' },
          ],
        },
      },
      {
        path: 'institusi',
        name: 'admin-institutions',
        component: PlaceholderPage,
        props: {
          title: 'Manajemen Institusi',
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Institusi' },
          ],
        },
      },
      {
        path: 'alumni',
        name: 'admin-alumni',
        component: PlaceholderPage,
        props: {
          title: 'Manajemen Alumni',
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Alumni' },
          ],
        },
      },
      {
        path: 'alumni/:id',
        name: 'admin-alumni-detail',
        component: PlaceholderPage,
        props: (route) => ({
          title: `Detail Alumni #${route.params.id}`,
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Alumni', to: '/admin/alumni' },
            { label: `Detail #${route.params.id}` },
          ],
        }),
      },
      {
        path: 'permohonan-alumni',
        name: 'admin-alumni-requests',
        component: PlaceholderPage,
        props: {
          title: 'Permohonan Alumni',
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Permohonan Alumni' },
          ],
        },
      },
      {
        path: 'kategori-kuesioner',
        name: 'admin-questionnaire-categories',
        component: PlaceholderPage,
        props: {
          title: 'Kategori Kuesioner',
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Kategori Kuesioner' },
          ],
        },
      },
      {
        path: 'tipe-jawaban',
        name: 'admin-answer-types',
        component: PlaceholderPage,
        props: {
          title: 'Tipe Jawaban',
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Tipe Jawaban' },
          ],
        },
      },
      {
        path: 'kuesioner',
        name: 'admin-questionnaires',
        component: PlaceholderPage,
        props: {
          title: 'Manajemen Kuesioner',
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Kuesioner' },
          ],
        },
      },
      {
        path: 'kuesioner/:id/pertanyaan',
        name: 'admin-questionnaire-questions',
        component: PlaceholderPage,
        props: (route) => ({
          title: `Pertanyaan Kuesioner #${route.params.id}`,
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Kuesioner', to: '/admin/kuesioner' },
            { label: 'Pertanyaan' },
          ],
        }),
      },
      {
        path: 'tracer-study',
        name: 'admin-tracer-studies',
        component: PlaceholderPage,
        props: {
          title: 'Tracer Study',
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Tracer Study' },
          ],
        },
      },
      {
        path: 'tracer-study/:id',
        name: 'admin-tracer-study-detail',
        component: PlaceholderPage,
        props: (route) => ({
          title: `Detail Tracer Study #${route.params.id}`,
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Tracer Study', to: '/admin/tracer-study' },
            { label: `Detail #${route.params.id}` },
          ],
        }),
      },
      {
        path: 'laporan',
        name: 'admin-reports',
        component: PlaceholderPage,
        props: {
          title: 'Laporan & Analitik',
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Laporan' },
          ],
        },
      },
      {
        path: 'pengaturan',
        name: 'admin-settings',
        component: PlaceholderPage,
        props: {
          title: 'Pengaturan Sistem',
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Pengaturan' },
          ],
        },
      },
      {
        path: 'audit-trail',
        name: 'admin-audit-trail',
        component: PlaceholderPage,
        props: {
          title: 'Audit Trail',
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Audit Trail' },
          ],
        },
      },
      {
        path: 'activity-log',
        name: 'admin-activity-log',
        component: PlaceholderPage,
        props: {
          title: 'Activity Log',
          breadcrumbs: [
            { label: 'Admin', to: '/admin/dashboard' },
            { label: 'Activity Log' },
          ],
        },
      },
    ],
  },
  {
    path: '/alumni',
    component: () => import('@/layouts/AlumniLayout.vue'),
    meta: { requiresAuth: true, role: 'alumni' },
    children: [
      {
        path: 'dashboard',
        name: 'alumni-dashboard',
        component: () => import('@/pages/alumni/DashboardPage.vue'),
      },
      {
        path: 'profil',
        name: 'alumni-profile',
        component: PlaceholderPage,
        props: {
          title: 'Profil Alumni',
          breadcrumbs: [
            { label: 'Alumni', to: '/alumni/dashboard' },
            { label: 'Profil' },
          ],
        },
      },
      {
        path: 'pekerjaan',
        name: 'alumni-employment',
        component: PlaceholderPage,
        props: {
          title: 'Riwayat Pekerjaan',
          breadcrumbs: [
            { label: 'Alumni', to: '/alumni/dashboard' },
            { label: 'Pekerjaan' },
          ],
        },
      },
      {
        path: 'permohonan',
        name: 'alumni-requests',
        component: PlaceholderPage,
        props: {
          title: 'Permohonan Alumni',
          breadcrumbs: [
            { label: 'Alumni', to: '/alumni/dashboard' },
            { label: 'Permohonan' },
          ],
        },
      },
      {
        path: 'employer',
        name: 'alumni-employer',
        component: PlaceholderPage,
        props: {
          title: 'Undangan Employer',
          breadcrumbs: [
            { label: 'Alumni', to: '/alumni/dashboard' },
            { label: 'Employer' },
          ],
        },
      },
      {
        path: 'tracer-study',
        name: 'alumni-tracer-studies',
        component: PlaceholderPage,
        props: {
          title: 'Tracer Study Aktif',
          breadcrumbs: [
            { label: 'Alumni', to: '/alumni/dashboard' },
            { label: 'Tracer Study' },
          ],
        },
      },
      {
        path: 'tracer-study/:id/isi',
        name: 'alumni-tracer-study-fill',
        component: PlaceholderPage,
        props: (route) => ({
          title: `Isi Tracer Study #${route.params.id}`,
          breadcrumbs: [
            { label: 'Alumni', to: '/alumni/dashboard' },
            { label: 'Tracer Study', to: '/alumni/tracer-study' },
            { label: 'Isi' },
          ],
        }),
      },
    ],
  },
  {
    path: '/403',
    name: 'forbidden',
    component: () => import('@/pages/errors/ForbiddenPage.vue'),
  },
  {
    path: '/',
    redirect: '/login',
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/pages/errors/NotFoundPage.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()
  const ui = useUIStore()

  if (typeof window !== 'undefined' && !ui.theme) {
    ui.hydrate()
  }

  if (!auth.bootstrapped) {
    await auth.bootstrapAuth()
  }

  const roleRedirectMap = {
    super_admin: '/admin/dashboard',
    alumni: '/alumni/dashboard',
  }

  if (to.meta.guestOnly && auth.isLoggedIn) {
    return next(roleRedirectMap[auth.user?.role] ?? '/login')
  }

  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    return next('/login')
  }

  if (to.meta.requiresAuth && auth.isLoggedIn && to.meta.role && auth.user?.role !== to.meta.role) {
    return next('/403')
  }

  next()
})

export default router