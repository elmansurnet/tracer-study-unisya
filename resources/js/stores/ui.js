import { defineStore } from 'pinia'
import { ref } from 'vue'

const THEME_KEY = 'tracer_study_theme'

export const useUIStore = defineStore('ui', () => {
  const sidebarOpen = ref(true)
  const theme = ref('light')
  const loading = ref(false)
  const loadingMessage = ref('Memproses...')

  function applyTheme(nextTheme) {
    theme.value = nextTheme
    document.documentElement.classList.toggle('dark', nextTheme === 'dark')
    localStorage.setItem(THEME_KEY, nextTheme)
  }

  function hydrate() {
    const savedTheme = localStorage.getItem(THEME_KEY)
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    applyTheme(savedTheme || (systemPrefersDark ? 'dark' : 'light'))
  }

  function toggleTheme() {
    applyTheme(theme.value === 'dark' ? 'light' : 'dark')
  }

  function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value
  }

  function setLoading(state, message = 'Memproses...') {
    loading.value = state
    loadingMessage.value = message
  }

  return {
    sidebarOpen,
    theme,
    loading,
    loadingMessage,
    hydrate,
    toggleTheme,
    toggleSidebar,
    setLoading,
  }
})