import { ref, reactive } from 'vue'
import { defineStore } from 'pinia'
import api from '@/services/api'

export const useSettingStore = defineStore('setting', () => {
  // ─── State ───────────────────────────────────────────────────────
  const groups      = ref({})          // { university: [...], tracer: [...], ... }
  const isLoading   = ref(false)
  const isSaving    = ref(false)
  const error       = ref(null)
  const successMsg  = ref(null)

  // ─── Actions ─────────────────────────────────────────────────────
  async function fetchAll(group = null) {
    isLoading.value = true
    error.value     = null
    try {
      const params = group ? { group } : {}
      const { data } = await api.get('/admin/settings', { params })
      groups.value = data.data
    } catch (err) {
      error.value = err?.response?.data?.message ?? 'Gagal memuat pengaturan.'
    } finally {
      isLoading.value = false
    }
  }

  async function updateSingle(group, key, value) {
    isSaving.value  = true
    error.value     = null
    successMsg.value = null
    try {
      const { data } = await api.put(`/admin/settings/${group}/${key}`, { value })
      successMsg.value = data.message
      // Refresh data setelah save
      await fetchAll()
    } catch (err) {
      error.value = err?.response?.data?.message ?? 'Gagal menyimpan pengaturan.'
      throw err
    } finally {
      isSaving.value = false
    }
  }

  async function batchUpdate(settings) {
    isSaving.value   = true
    error.value      = null
    successMsg.value = null
    try {
      const { data } = await api.put('/admin/settings/batch', { settings })
      successMsg.value = data.message
      await fetchAll()
    } catch (err) {
      error.value = err?.response?.data?.errors ?? err?.response?.data?.message ?? 'Gagal menyimpan pengaturan.'
      throw err
    } finally {
      isSaving.value = false
    }
  }

  function clearMessages() {
    error.value      = null
    successMsg.value = null
  }

  return {
    groups, isLoading, isSaving, error, successMsg,
    fetchAll, updateSingle, batchUpdate, clearMessages,
  }
})
