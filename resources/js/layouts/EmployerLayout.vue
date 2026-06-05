<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <header class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
      <div class="mx-auto flex h-16 max-w-5xl items-center justify-between px-4 sm:px-6">
        <div class="flex items-center gap-3">
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-600 text-sm font-bold text-white dark:bg-primary-500 dark:text-slate-950">
            U
          </div>
          <div>
            <p class="font-display text-sm font-semibold text-slate-900 dark:text-white">
              Tracer Study UNISYA
            </p>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              Portal Pengguna Alumni
            </p>
          </div>
        </div>

        <button
          type="button"
          class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
          :aria-label="ui.isDarkMode ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'"
          @click="ui.toggleTheme()"
        >
          {{ ui.isDarkMode ? '☀️' : '🌙' }}
        </button>
      </div>
    </header>

    <main class="mx-auto max-w-[800px] px-4 py-8 sm:px-6">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { RouterView } from 'vue-router'
import { useUIStore } from '@/stores/ui'

const ui = useUIStore()

onMounted(() => {
  ui.hydrate()
})
</script>