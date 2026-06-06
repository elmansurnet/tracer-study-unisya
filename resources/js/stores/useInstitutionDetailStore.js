import { ref } from 'vue'
import { defineStore } from 'pinia'
import api from '@/services/api'

export const useInstitutionDetailStore = defineStore('institutionDetail', () => {
  // ─── State ───────────────────────────────────────────────────────
  const detail      = ref(null)
  const isLoading   = ref(false)
  const isSaving    = ref(false)
  const error       = ref(null)
  const successMsg  = ref(null)

  // ─── Actions ─────────────────────────────────────────────────────
  async function fetchByInstitution(institutionId) {
    isLoading.value = true
    error.value     = null
    try {
      const { data } = await api.get(`/admin/institutions/${institutionId}/detail`)
      detail.value = data.data
    } catch (err) {
      error.value = err?.response?.data?.message ?? 'Gagal memuat detail institusi.'
    } finally {
      isLoading.value = false
    }
  }

  async function save(institutionId, payload) {
    isSaving.value   = true
    error.value      = null
    successMsg.value = null
    try {
      const { data } = await api.put(`/admin/institutions/${institutionId}/detail`, payload)
      detail.value     = data.data
      successMsg.value = data.message
    } catch (err) {
      if (err?.response?.status === 422) {
        error.value = err.response.data.errors
      } else {
        error.value = err?.response?.data?.message ?? 'Gagal menyimpan detail institusi.'
      }
      throw err
    } finally {
      isSaving.value = false
    }
  }

  function clearMessages() {
    error.value      = null
    successMsg.value = null
  }

  function reset() {
    detail.value     = null
    error.value      = null
    successMsg.value = null
  }

  return {
    detail, isLoading, isSaving, error, successMsg,
    fetchByInstitution, save, clearMessages, reset,
  }
})
