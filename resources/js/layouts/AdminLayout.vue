<template>
  <div class="min-h-screen bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100">
    <a
      href="#main-content"
      class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-xl focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-primary-700"
    >
      Lewati ke konten utama
    </a>

    <div class="flex min-h-screen">
      <div
        v-if="sidebarVisible"
        class="fixed inset-0 z-40 bg-slate-950/40 lg:hidden"
        @click="ui.closeSidebar()"
      />

      <div
        class="fixed inset-y-0 left-0 z-50 transform transition-transform duration-200 lg:static lg:z-auto"
        :class="sidebarVisible ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
      >
        <AppSidebar
          role="super_admin"
          :user-name="displayName"
        />
      </div>

      <div class="min-w-0 flex-1">
        <AppHeader
          :title="ui.pageTitle"
          :breadcrumbs="ui.breadcrumbs"
          :user-name="displayName"
          user-role="Super Admin"
          :notification-count="3"
          @toggleSidebar="ui.toggleSidebar()"
        />

        <main id="main-content" class="px-4 py-6 sm:px-6">
          <RouterView />
        </main>
      </div>
    </div>

    <div
      v-if="ui.notificationPanelOpen"
      class="fixed inset-y-0 right-0 z-50 w-full max-w-sm border-l border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900"
    >
      <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-800">
        <div>
          <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Notifikasi</h2>
          <p class="text-xs text-slate-500 dark:text-slate-400">Pembaruan sistem terbaru</p>
        </div>
        <button
          type="button"
          class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800"
          @click="ui.closeNotificationPanel()"
        >
          ✕
        </button>
      </div>

      <div class="space-y-3 p-5">
        <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-800">
          <p class="text-sm font-semibold text-slate-900 dark:text-white">
            Session 1C aktif
          </p>
          <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            Layout utama dan routing sedang dibangun untuk admin, alumni, dan employer.
          </p>
        </div>

        <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-800">
          <p class="text-sm font-semibold text-slate-900 dark:text-white">
            Verifikasi auth refresh
          </p>
          <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            Pastikan sesi pengguna tetap sinkron saat browser dimuat ulang.
          </p>
        </div>
      </div>
    </div>

    <div
      v-if="ui.isLoading"
      class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 px-4"
    >
      <div class="w-full max-w-sm rounded-3xl bg-white p-6 text-center shadow-2xl dark:bg-slate-900">
        <div class="mx-auto mb-4 h-12 w-12 animate-spin rounded-full border-4 border-primary-200 border-t-primary-600 dark:border-primary-500/20 dark:border-t-primary-400" />
        <p class="text-sm font-semibold text-slate-900 dark:text-white">
          {{ ui.loadingMessage }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { RouterView } from 'vue-router'
import AppHeader from '@/components/navigation/AppHeader.vue'
import AppSidebar from '@/components/navigation/AppSidebar.vue'
import { useAuthStore } from '@/stores/auth'
import { useUIStore } from '@/stores/ui'

const auth = useAuthStore()
const ui = useUIStore()

const displayName = computed(() => auth.user?.name || 'Super Administrator')
const sidebarVisible = computed(() => ui.sidebarOpen)

onMounted(() => {
  ui.hydrate()
})
</script>