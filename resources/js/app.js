import { createApp } from 'vue'
import { createPinia } from 'pinia'
import axios from 'axios'
import App from './App.vue'
import router from './router'
import { useAuthStore } from '@/stores/auth'
import { useUIStore } from '@/stores/ui'
import '../css/app.css'

// ---------------------------------------------------------------------------
// Axios global config
// ---------------------------------------------------------------------------
axios.defaults.baseURL = '/api/v1'
axios.defaults.withCredentials = true
axios.defaults.withXSRFToken = true
axios.defaults.headers.common.Accept = 'application/json'
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// ---------------------------------------------------------------------------
// Axios response interceptor — handle global 401
// Interceptor didefinisikan SEBELUM app di-mount agar aktif sejak awal.
// ---------------------------------------------------------------------------
axios.interceptors.response.use(
  (response) => response,
  async (error) => {
    const status = error.response?.status

    if (status === 401) {
      const auth = useAuthStore()
      auth.clearAuth()

      if (router.currentRoute.value.meta.requiresAuth) {
        await router.push({ name: 'login' })
      }
    }

    return Promise.reject(error)
  },
)

// ---------------------------------------------------------------------------
// Bootstrap — dibungkus async IIFE agar:
// 1. Kompatibel dengan semua Vite build target (termasuk es2020)
// 2. Error bootstrap tertangkap dan tidak menyebabkan blank screen diam
// 3. Urutan eksekusi terjamin: pinia → ui.hydrate → auth.bootstrap → router → mount
// ---------------------------------------------------------------------------
;(async () => {
  const app = createApp(App)
  const pinia = createPinia()

  app.use(pinia)

  // Hydrate UI store (tema, sidebar) sebelum render pertama
  const ui = useUIStore()
  ui.hydrate()

  // Bootstrap auth: cek token aktif, load user dari /api/v1/auth/me
  // Jika gagal (network error, token expired) clearAuth() dipanggil di store
  const auth = useAuthStore()
  await auth.bootstrapAuth()

  app.use(router)
  app.mount('#app')
})()
