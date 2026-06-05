<template>
  <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-slate-950/90">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6">
      <div class="flex items-center gap-3">
        <button
          type="button"
          class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 lg:hidden"
          aria-label="Buka menu navigasi"
          @click="$emit('toggleSidebar')"
        >
          ☰
        </button>

        <div>
          <p class="text-xs font-medium uppercase tracking-wide text-slate-400 dark:text-slate-500">
            Tracer Study UNISYA
          </p>
          <h1 class="font-display text-lg font-semibold text-slate-900 dark:text-white">
            {{ title }}
          </h1>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <button
          type="button"
          class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
          :aria-label="ui.isDarkMode ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'"
          @click="ui.toggleTheme()"
        >
          {{ ui.isDarkMode ? '☀️' : '🌙' }}
        </button>

        <button
          type="button"
          class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
          aria-label="Lihat notifikasi"
          @click="ui.toggleNotificationPanel()"
        >
          🔔
          <span
            v-if="notificationCount > 0"
            class="absolute right-1.5 top-1.5 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-semibold text-white"
          >
            {{ notificationCount }}
          </span>
        </button>

        <div class="hidden items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-900 sm:flex">
          <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-600 text-sm font-semibold text-white dark:bg-primary-500 dark:text-slate-950">
            {{ initials }}
          </div>
          <div class="text-left">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">
              {{ userName }}
            </p>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              {{ userRole }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800 sm:px-6">
      <AppBreadcrumb :items="breadcrumbs" />
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useUIStore } from '@/stores/ui'
import AppBreadcrumb from './AppBreadcrumb.vue'

const props = defineProps({
  title: {
    type: String,
    default: 'Dashboard',
  },
  breadcrumbs: {
    type: Array,
    default: () => [],
  },
  userName: {
    type: String,
    default: 'Pengguna',
  },
  userRole: {
    type: String,
    default: 'Pengguna',
  },
  notificationCount: {
    type: Number,
    default: 0,
  },
})

defineEmits(['toggleSidebar'])

const ui = useUIStore()

const initials = computed(() => {
  return props.userName
    .split(' ')
    .slice(0, 2)
    .map((part) => part.charAt(0).toUpperCase())
    .join('')
})
</script>