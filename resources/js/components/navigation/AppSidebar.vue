<template>
  <aside
    class="flex h-full w-[240px] flex-col border-r border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950"
  >
    <div class="flex h-16 items-center gap-3 border-b border-slate-200 px-5 dark:border-slate-800">
      <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-600 text-sm font-bold text-white dark:bg-primary-500 dark:text-slate-950">
        U
      </div>
      <div>
        <p class="font-display text-sm font-semibold text-slate-900 dark:text-white">
          Tracer Study
        </p>
        <p class="text-xs text-slate-500 dark:text-slate-400">UNISYA</p>
      </div>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-5">
      <div
        v-for="group in menu"
        :key="group.title"
        class="space-y-2"
      >
        <p class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
          {{ group.title }}
        </p>

        <RouterLink
          v-for="item in group.items"
          :key="item.to"
          :to="item.to"
          class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition"
          :class="isActive(item) ? activeClass : inactiveClass"
        >
          <span class="text-base">{{ item.icon }}</span>
          <span>{{ item.label }}</span>
        </RouterLink>
      </div>
    </nav>

    <div class="border-t border-slate-200 px-4 py-4 dark:border-slate-800">
      <div class="rounded-2xl bg-slate-50 p-3 dark:bg-slate-900">
        <p class="text-sm font-semibold text-slate-900 dark:text-white">
          {{ userName }}
        </p>
        <p class="text-xs text-slate-500 dark:text-slate-400">
          {{ userRoleLabel }}
        </p>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { RouterLink, useRoute } from 'vue-router'

const props = defineProps({
  role: {
    type: String,
    required: true,
  },
  userName: {
    type: String,
    default: 'Pengguna',
  },
})

const route = useRoute()

const adminMenu = [
  {
    title: 'Utama',
    items: [
      { label: 'Dashboard', to: '/admin/dashboard', icon: '🏠' },
      { label: 'Alumni', to: '/admin/alumni', icon: '🎓' },
      { label: 'Kuesioner', to: '/admin/kuesioner', icon: '📝' },
      { label: 'Tracer Study', to: '/admin/tracer-study', icon: '📊' },
      { label: 'Laporan', to: '/admin/laporan', icon: '📈' },
    ],
  },
  {
    title: 'Master Data',
    items: [
      { label: 'Pengguna', to: '/admin/pengguna', icon: '👤' },
      { label: 'Fakultas', to: '/admin/fakultas', icon: '🏛️' },
      { label: 'Program Studi', to: '/admin/program-studi', icon: '📚' },
      { label: 'Kategori Profesi', to: '/admin/kategori-profesi', icon: '🗂️' },
      { label: 'Profesi', to: '/admin/profesi', icon: '💼' },
      { label: 'Institusi', to: '/admin/institusi', icon: '🏢' },
    ],
  },
  {
    title: 'Sistem',
    items: [
      { label: 'Pengaturan', to: '/admin/pengaturan', icon: '⚙️' },
      { label: 'Audit Trail', to: '/admin/audit-trail', icon: '🛡️' },
      { label: 'Activity Log', to: '/admin/activity-log', icon: '📜' },
    ],
  },
]

const alumniMenu = [
  {
    title: 'Utama',
    items: [
      { label: 'Dashboard', to: '/alumni/dashboard', icon: '🏠' },
      { label: 'Profil', to: '/alumni/profil', icon: '👤' },
      { label: 'Pekerjaan', to: '/alumni/pekerjaan', icon: '💼' },
      { label: 'Tracer Study', to: '/alumni/tracer-study', icon: '📝' },
      { label: 'Permohonan', to: '/alumni/permohonan', icon: '📨' },
      { label: 'Employer', to: '/alumni/employer', icon: '🏢' },
    ],
  },
]

const menu = computed(() => (props.role === 'super_admin' ? adminMenu : alumniMenu))

const userRoleLabel = computed(() =>
  props.role === 'super_admin' ? 'Super Admin' : 'Alumni',
)

const activeClass =
  'bg-primary-50 text-primary-700 dark:bg-primary-500/15 dark:text-primary-300'

const inactiveClass =
  'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white'

function isActive(item) {
  return route.path === item.to || route.path.startsWith(`${item.to}/`)
}
</script>