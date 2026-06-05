<template>
  <section class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm font-semibold uppercase tracking-[0.2em] text-primary-600 dark:text-primary-300">
        Portal Alumni
      </p>
      <h1 class="mt-3 font-display text-3xl font-bold text-slate-900 dark:text-white">
        Selamat datang di dashboard alumni
      </h1>
      <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600 dark:text-slate-400">
        Gunakan area ini untuk memperbarui profil, mengelola riwayat pekerjaan, memantau tracer study aktif, dan mengundang employer untuk memberikan umpan balik.
      </p>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
      <RouterLink
        v-for="item in cards"
        :key="item.to"
        :to="item.to"
        class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-primary-500/40"
      >
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-2xl dark:bg-primary-500/15">
          {{ item.icon }}
        </div>
        <h2 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">
          {{ item.title }}
        </h2>
        <p class="mt-2 text-sm leading-7 text-slate-600 dark:text-slate-400">
          {{ item.description }}
        </p>
      </RouterLink>
    </div>
  </section>
</template>

<script setup>
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useUIStore } from '@/stores/ui'

const ui = useUIStore()

const cards = [
  {
    icon: '👤',
    title: 'Profil Alumni',
    description: 'Perbarui data diri, informasi akademik, dan kontak aktif.',
    to: '/alumni/profil',
  },
  {
    icon: '💼',
    title: 'Riwayat Pekerjaan',
    description: 'Catat pekerjaan, institusi, profesi, dan relevansi pekerjaan.',
    to: '/alumni/pekerjaan',
  },
  {
    icon: '📝',
    title: 'Tracer Study',
    description: 'Lihat sesi tracer study yang aktif dan isi kuesioner.',
    to: '/alumni/tracer-study',
  },
  {
    icon: '📨',
    title: 'Permohonan',
    description: 'Ajukan perubahan data yang membutuhkan persetujuan admin.',
    to: '/alumni/permohonan',
  },
  {
    icon: '🏢',
    title: 'Employer',
    description: 'Undang employer untuk mengisi umpan balik terhadap kinerja Anda.',
    to: '/alumni/employer',
  },
]

onMounted(() => {
  ui.setPageTitle('Dashboard Alumni')
  ui.setBreadcrumbs([
    { label: 'Alumni', to: '/alumni/dashboard' },
    { label: 'Dashboard' },
  ])
})
</script>