<template>
  <section class="space-y-6">
    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-8 shadow-sm dark:border-slate-700 dark:bg-slate-900">
      <AppBadge variant="warning">Dalam Pengembangan</AppBadge>
      <h1 class="mt-4 font-display text-3xl font-bold text-slate-900 dark:text-white">
        {{ title }}
      </h1>
      <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600 dark:text-slate-400">
        Halaman ini telah disiapkan sebagai shell navigasi Session 1C dan akan diisi fungsionalitas penuh pada sesi/phase terkait sesuai phase tracker proyek.
      </p>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
      <div
        v-for="index in 3"
        :key="index"
        class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
      >
        <AppSkeleton class-name="h-5 w-2/5" />
        <AppSkeleton class-name="mt-4 h-4 w-full" />
        <AppSkeleton class-name="mt-2 h-4 w-11/12" />
        <AppSkeleton class-name="mt-6 h-10 w-32" />
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted } from 'vue'
import AppBadge from '@/components/base/AppBadge.vue'
import AppSkeleton from '@/components/base/AppSkeleton.vue'
import { useUIStore } from '@/stores/ui'

const props = defineProps({
  title: {
    type: String,
    default: 'Halaman',
  },
  breadcrumbs: {
    type: Array,
    default: () => [],
  },
})

const ui = useUIStore()

onMounted(() => {
  ui.setPageTitle(props.title)
  ui.setBreadcrumbs(props.breadcrumbs)
})
</script>