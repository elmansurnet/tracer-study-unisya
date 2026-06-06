import { ref, reactive } from 'vue'
import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuditTrailStore = defineStore('auditTrail', () => {
  // ─── State ───────────────────────────────────────────────────────
  const list        = ref([])
  const selected    = ref(null)
  const isLoading   = ref(false)
  const isLoadingDetail = ref(false)
  const error       = ref(null)
  const filters     = reactive({
    search:    '',
    user_id:   '',
    action:    '',
    model:     '',
    date_from: '',
    date_to:   '',
    per_page:  20,
  })
  const pagination  = reactive({
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
      // Bersihkan parameter kosong
      Object.keys(params).forEach(k => {
        if (params[k] === '' || params[k] === null) delete params[k]
      })
      const { data } = await api.get('/admin/audit-trails', { params })
      list.value = data.data
      Object.assign(pagination, data.meta)
    } catch (err) {
      error.value = err?.response?.data?.message ?? 'Gagal memuat data audit trail.'
    } finally {
      isLoading.value = false
    }
  }

  async function fetchDetail(id) {
    isLoadingDetail.value = true
    error.value           = null
    try {
      const { data } = await api.get(`/admin/audit-trails/${id}`)
      selected.value = data.data
    } catch (err) {
      error.value = err?.response?.data?.message ?? 'Gagal memuat detail audit trail.'
    } finally {
      isLoadingDetail.value = false
    }
  }

  function resetFilters() {
    Object.assign(filters, {
      search: '', user_id: '', action: '', model: '',
      date_from: '', date_to: '', per_page: 20,
    })
  }

  function clearSelected() {
    selected.value = null
  }

  return {
    list, selected, isLoading, isLoadingDetail, error, filters, pagination,
    fetchList, fetchDetail, resetFilters, clearSelected,
  }
})
