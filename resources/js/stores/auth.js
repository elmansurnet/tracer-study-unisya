import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import axios from 'axios'

const AUTH_TOKEN_KEY = 'tracer_study_auth_token'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem(AUTH_TOKEN_KEY) || '')
  const permissions = ref([])
  const bootstrapped = ref(false)
  const authLoading = ref(false)
  const otpContext = ref({
    identifier: '',
    identifierType: 'email',
    expiresIn: 300,
    requestedAt: null,
  })

  const isLoggedIn = computed(() => Boolean(token.value && user.value))
  const userRole = computed(() => user.value?.role ?? null)

  function applyToken(accessToken = '') {
    token.value = accessToken || ''

    if (token.value) {
      localStorage.setItem(AUTH_TOKEN_KEY, token.value)
      axios.defaults.headers.common.Authorization = `Bearer ${token.value}`
    } else {
      localStorage.removeItem(AUTH_TOKEN_KEY)
      delete axios.defaults.headers.common.Authorization
    }
  }

  function setAuth(userData, accessToken = token.value, permissionData = []) {
    user.value = userData ?? null
    permissions.value = Array.isArray(permissionData) ? permissionData : []

    if (accessToken) {
      applyToken(accessToken)
    }
  }

  function setOtpContext(payload = {}) {
    otpContext.value = {
      identifier: payload.identifier ?? '',
      identifierType: payload.identifierType ?? 'email',
      expiresIn: Number(payload.expiresIn ?? 300),
      requestedAt: payload.requestedAt ?? new Date().toISOString(),
    }
  }

  function clearOtpContext() {
    otpContext.value = {
      identifier: '',
      identifierType: 'email',
      expiresIn: 300,
      requestedAt: null,
    }
  }

  function clearAuth() {
    user.value = null
    permissions.value = []
    applyToken('')
    clearOtpContext()
  }

  async function ensureCsrfCookie() {
    await axios.get('/sanctum/csrf-cookie', { baseURL: '' })
  }

  async function login(payload) {
    authLoading.value = true

    try {
      await ensureCsrfCookie()

      const { data } = await axios.post('/auth/login', {
        email: payload.email,
        password: payload.password,
      })

      const accessToken = data?.data?.token ?? ''
      const authUser = data?.data?.user ?? null
      const authPermissions = data?.data?.permissions ?? []

      setAuth(authUser, accessToken, authPermissions)
      bootstrapped.value = true

      return data
    } finally {
      authLoading.value = false
    }
  }

  async function requestOtp(payload) {
    authLoading.value = true

    try {
      await ensureCsrfCookie()

      const { data } = await axios.post('/auth/otp/request', {
        identifier: payload.identifier,
        identifier_type: payload.identifierType,
      })

      setOtpContext({
        identifier: payload.identifier,
        identifierType: payload.identifierType,
        expiresIn: data?.data?.expires_in ?? 300,
        requestedAt: new Date().toISOString(),
      })

      return data
    } finally {
      authLoading.value = false
    }
  }

  async function verifyOtp(payload) {
    authLoading.value = true

    try {
      await ensureCsrfCookie()

      const { data } = await axios.post('/auth/otp/verify', {
        identifier: payload.identifier,
        otp_code: payload.otpCode,
      })

      const accessToken = data?.data?.token ?? ''
      const authUser = data?.data?.user ?? null
      const authPermissions = data?.data?.permissions ?? []

      setAuth(authUser, accessToken, authPermissions)
      clearOtpContext()
      bootstrapped.value = true

      return data
    } finally {
      authLoading.value = false
    }
  }

  async function fetchMe() {
    const { data } = await axios.get('/auth/me')
    const authUser = data?.data?.user ?? null
    const authPermissions = data?.data?.permissions ?? []

    user.value = authUser
    permissions.value = authPermissions

    return data
  }

  async function bootstrapAuth() {
    if (bootstrapped.value) {
      return
    }

    if (!token.value) {
      bootstrapped.value = true
      return
    }

    authLoading.value = true
    applyToken(token.value)

    try {
      await fetchMe()
    } catch (error) {
      clearAuth()
    } finally {
      bootstrapped.value = true
      authLoading.value = false
    }
  }

  async function logout() {
    try {
      if (token.value) {
        await axios.post('/auth/logout')
      }
    } catch (error) {
      // Silent fail: token mungkin sudah invalid
    } finally {
      clearAuth()
      bootstrapped.value = true
    }
  }

  return {
    user,
    token,
    permissions,
    bootstrapped,
    authLoading,
    otpContext,
    isLoggedIn,
    userRole,
    applyToken,
    setAuth,
    setOtpContext,
    clearOtpContext,
    clearAuth,
    login,
    requestOtp,
    verifyOtp,
    fetchMe,
    bootstrapAuth,
    logout,
  }
})