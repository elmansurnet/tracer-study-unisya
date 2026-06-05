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
          role="alumni"
          :user-name="displayName"
        />
      </div>

      <div class="min-w-0 flex-1">
        <AppHeader
          :title="ui.pageTitle"
          :breadcrumbs="ui.breadcrumbs"
          :user-name="displayName"
          user-role="Alumni"
          :notification-count="2"
          @toggleSidebar="ui.toggleSidebar()"
        />

        <main id="main-content" class="px-4 py-6 sm:px-6">
          <RouterView />
        </main>
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

const displayName = computed(() => auth.user?.name || 'Alumni')
const sidebarVisible = computed(() => ui.sidebarOpen)

onMounted(() => {
  ui.hydrate()
})
</script>