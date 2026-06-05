import { defineStore } from 'pinia'
import { computed, ref, watch } from 'vue'

const STORAGE_KEY = 'tracer-study-unisya-ui'

export const useUIStore = defineStore('ui', () => {
  const sidebarOpen = ref(true)
  const theme = ref('light')
  const isLoading = ref(false)
  const loadingMessage = ref('Memuat data...')
  const pageTitle = ref('Dashboard')
  const notificationPanelOpen = ref(false)
  const breadcrumbs = ref([])

  const isDarkMode = computed(() => theme.value === 'dark')

  function hydrate() {
    try {
      const raw = window.localStorage.getItem(STORAGE_KEY)
      if (raw) {
        const parsed = JSON.parse(raw)
        sidebarOpen.value = parsed.sidebarOpen ?? true
        theme.value = parsed.theme ?? detectPreferredTheme()
      } else {
        theme.value = detectPreferredTheme()
      }
    } catch {
      theme.value = detectPreferredTheme()
    }

    applyTheme(theme.value)
  }

  function detectPreferredTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
  }

  function persist() {
    window.localStorage.setItem(
      STORAGE_KEY,
      JSON.stringify({
        sidebarOpen: sidebarOpen.value,
        theme: theme.value,
      }),
    )
  }

  function applyTheme(value) {
    const root = document.documentElement

    if (value === 'dark') {
      root.classList.add('dark')
    } else {
      root.classList.remove('dark')
    }
  }

  function setTheme(value) {
    theme.value = value === 'dark' ? 'dark' : 'light'
    applyTheme(theme.value)
    persist()
  }

  function toggleTheme() {
    setTheme(isDarkMode.value ? 'light' : 'dark')
  }

  function openSidebar() {
    sidebarOpen.value = true
  }

  function closeSidebar() {
    sidebarOpen.value = false
  }

  function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value
  }

  function setLoading(state, message = 'Memuat data...') {
    isLoading.value = state
    loadingMessage.value = message
  }

  function setPageTitle(title) {
    pageTitle.value = title || 'Dashboard'
    document.title = `${pageTitle.value} • Tracer Study UNISYA`
  }

  function setBreadcrumbs(items = []) {
    breadcrumbs.value = items
  }

  function toggleNotificationPanel() {
    notificationPanelOpen.value = !notificationPanelOpen.value
  }

  function closeNotificationPanel() {
    notificationPanelOpen.value = false
  }

  watch(sidebarOpen, persist)

  return {
    sidebarOpen,
    theme,
    isDarkMode,
    isLoading,
    loadingMessage,
    pageTitle,
    breadcrumbs,
    notificationPanelOpen,
    hydrate,
    setTheme,
    toggleTheme,
    openSidebar,
    closeSidebar,
    toggleSidebar,
    setLoading,
    setPageTitle,
    setBreadcrumbs,
    toggleNotificationPanel,
    closeNotificationPanel,
  }
})