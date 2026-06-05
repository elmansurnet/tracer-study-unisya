import { createApp } from 'vue'
import { createPinia } from 'pinia'
import axios from 'axios'
import App from './App.vue'
import router from './router'
import { useAuthStore } from '@/stores/auth'
import { useUIStore } from '@/stores/ui'
import '../css/app.css'

axios.defaults.baseURL = '/api/v1'
axios.defaults.withCredentials = true
axios.defaults.withXSRFToken = true
axios.defaults.headers.common.Accept = 'application/json'
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

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

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)

const ui = useUIStore()
ui.hydrate()

const auth = useAuthStore()
await auth.bootstrapAuth()

app.use(router)
app.mount('#app')