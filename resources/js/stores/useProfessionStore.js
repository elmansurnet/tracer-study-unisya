import { defineStore } from 'pinia'
import { ref } from 'vue'
import http from '@/lib/http'

export const useProfessionStore = defineStore('profession', () => {
  const professions    = ref([])
  const meta           = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0, per_page: 15 })
  const allProfessions = ref([])   // untuk dropdown
  const loading        = ref(false)
  const errors         = ref({})

  async function fetchProfessions(params = {}) {
    loading.value = true
    errors.value  = {}
    try {
      const { data } = await http.get('/admin/professions', { params })
      professions.value = data.data
      meta.value        = data.meta ?? meta.value
    } finally {
      loading.value = false
    }
  }

  async function fetchAllProfessions(categoryId = null) {
    try {
      const params = categoryId ? { profession_category_id: categoryId } : {}
      const { data } = await http.get('/admin/professions/all', { params })
      allProfessions.value = data.data
    } catch {}
  }

  async function createProfession(payload) {
    errors.value = {}
    const { data } = await http.post('/admin/professions', payload)
    return data
  }

  async function updateProfession(id, payload) {
    errors.value = {}
    const { data } = await http.put(`/admin/professions/${id}`, payload)
    return data
  }

  async function deleteProfession(id) {
    const { data } = await http.delete(`/admin/professions/${id}`)
    professions.value = professions.value.filter(p => p.id !== id)
    return data
  }

  async function restoreProfession(id) {
    const { data } = await http.patch(`/admin/professions/${id}/restore`)
    return data
  }

  function clearErrors() {
    errors.value = {}
  }

  return {
    professions, meta, allProfessions, loading, errors,
    fetchProfessions, fetchAllProfessions,
    createProfession, updateProfession,
    deleteProfession, restoreProfession,
    clearErrors,
  }
})
