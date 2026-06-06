import { ref, reactive } from 'vue'
import { defineStore } from 'pinia'
import api from '@/services/api'

export const useActivityLogStore = defineStore('activityLog', () => {
  // ─── State ───────────────────────────────────────────────────────
  const list            = ref([])
  const selected        = ref(null)
  const isLoading       = ref(false)
  const isLoadingDetail = ref(false)
  const isPurging       = ref(false)
  const error           = ref(null)
  const filters         = reactive({
    search:     '',
    log_name:   '',
    event:      '',
    causer_id:  '',
    date_from:  '',
    date_to:    '',
    per_page:   20,
  })
  const pagination = reactive({
    current_page: 1,
    last_page:    1,
    per_page:     20,
    total:        0,
  })

  // ─── Actions ─────────────────────────────────────────────────────
  async function fetchList(page = 1) {
    isLoading.value = true
    error.value     = null
    try {
      const params = { page, ...filters }
      Object.keys(params).forEach(k => {
        if (params[k] === '' || params[k] === null) delete params[k]
      })
      const { data } = await api.get('/admin/activity-logs', { params })
      list.value = data.data
      Object.assign(pagination, data.meta)
    } catch (err) {
      error.value = err?.response?.data?.message ?? 'Gagal memuat activity log.'
    } finally {
      isLoading.value = false
    }
  }

  async function fetchDetail(id) {
    isLoadingDetail.value = true
    error.value           = null
    try {
      const { data } = await api.get(`/admin/activity-logs/${id}`)
      selected.value = data.data
    } catch (err) {
      error.value = err?.response?.data?.message ?? 'Gagal memuat detail activity log.'
    } finally {
      isLoadingDetail.value = false
    }
  }

  async function purge(before = null) {
    isPurging.value = true
    error.value     = null
    try {
      const params = before ? { before } : {}
      const { data } = await api.delete('/admin/activity-logs', { params })
      await fetchList(1)
      return data.message
    } catch (err) {
      error.value = err?.response?.data?.message ?? 'Gagal membersihkan activity log.'
      throw err
    } finally {
      isPurging.value = false
    }
  }

  function resetFilters() {
    Object.assign(filters, {
      search: '', log_name: '', event: '', causer_id: '',
      date_from: '', date_to: '', per_page: 20,
    })
  }

  function clearSelected() {
    selected.value = null
  }

  return {
    list, selected, isLoading, isLoadingDetail, isPurging, error, filters, pagination,
    fetchList, fetchDetail, purge, resetFilters, clearSelected,
  }
})
