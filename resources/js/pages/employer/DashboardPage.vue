<template>
  <section class="space-y-6">
    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
      <p class="text-sm font-semibold uppercase tracking-[0.2em] text-primary-600 dark:text-primary-300">
        Dashboard Employer
      </p>
      <h1 class="mt-3 font-display text-3xl font-bold text-slate-900 dark:text-white">
        Selamat datang di portal employer
      </h1>
      <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-400">
        Portal ini digunakan untuk memberikan umpan balik terhadap alumni yang mengundang Anda pada tracer study UNISYA.
      </p>

      <div class="mt-8 grid gap-4 md:grid-cols-2">
        <div class="rounded-3xl bg-slate-50 p-5 dark:bg-slate-800/60">
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
            Status akses
          </p>
          <p class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">
            Terverifikasi
          </p>
        </div>

        <div class="rounded-3xl bg-slate-50 p-5 dark:bg-slate-800/60">
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
            Kuesioner tersedia
          </p>
          <p class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">
            1 kuesioner
          </p>
        </div>
      </div>

      <div class="mt-8">
        <AppButton @click="$router.push('/employer/kuesioner/1')">
          Mulai Isi Kuesioner
        </AppButton>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted } from 'vue'
import AppButton from '@/components/base/AppButton.vue'
import { useUIStore } from '@/stores/ui'

const ui = useUIStore()

onMounted(() => {
  ui.setPageTitle('Dashboard Employer')
  ui.setBreadcrumbs([{ label: 'Employer' }, { label: 'Dashboard' }])
})
</script>