<template>
  <section class="space-y-6">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <div
        v-for="item in kpis"
        :key="item.label"
        class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
      >
        <div class="flex items-start justify-between">
          <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
              {{ item.label }}
            </p>
            <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">
              {{ item.value }}
            </p>
          </div>
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-50 text-xl dark:bg-primary-500/15">
            {{ item.icon }}
          </div>
        </div>

        <p class="mt-4 text-xs text-slate-500 dark:text-slate-400">
          {{ item.description }}
        </p>
      </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
          Status implementasi fondasi
        </h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
          Shell admin telah siap untuk dipakai sebagai dasar Session 2A dan phase lanjutan.
        </p>

        <div class="mt-6 space-y-4">
          <div
            v-for="item in checklist"
            :key="item"
            class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/60"
          >
            <span class="text-emerald-600 dark:text-emerald-400">✔</span>
            <span class="text-sm text-slate-700 dark:text-slate-300">{{ item }}</span>
          </div>
        </div>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
          Navigasi utama
        </h2>

        <div class="mt-5 space-y-3">
          <RouterLink
            v-for="link in quickLinks"
            :key="link.to"
            :to="link.to"
            class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-primary-300 hover:bg-primary-50 hover:text-primary-700 dark:border-slate-800 dark:text-slate-200 dark:hover:border-primary-500/40 dark:hover:bg-primary-500/10 dark:hover:text-primary-300"
          >
            <span>{{ link.label }}</span>
            <span>→</span>
          </RouterLink>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useUIStore } from '@/stores/ui'

const ui = useUIStore()

const kpis = [
  { label: 'Total Alumni', value: '0', icon: '🎓', description: 'Menunggu integrasi data master alumni pada Phase 3.' },
  { label: 'Alumni Bekerja', value: '0', icon: '💼', description: 'Akan dihitung otomatis dari employment tracking.' },
  { label: 'Respons Tracer', value: '0%', icon: '📊', description: 'Nilai ini aktif setelah sesi tracer study tersedia.' },
  { label: 'Sesi Aktif', value: '0', icon: '🗓️', description: 'Belum ada sesi tracer study yang dipublikasikan.' },
]

const checklist = [
  'Admin layout dengan sidebar 240px telah aktif.',
  'Header, breadcrumb, dark mode, dan panel notifikasi tersedia.',
  'Route admin siap menjadi shell untuk modul Phase 2.',
]

const quickLinks = [
  { label: 'Kelola Pengguna', to: '/admin/pengguna' },
  { label: 'Kelola Fakultas', to: '/admin/fakultas' },
  { label: 'Kelola Program Studi', to: '/admin/program-studi' },
]

onMounted(() => {
  ui.setPageTitle('Dashboard Admin')
  ui.setBreadcrumbs([
    { label: 'Admin', to: '/admin/dashboard' },
    { label: 'Dashboard' },
  ])
})
</script>