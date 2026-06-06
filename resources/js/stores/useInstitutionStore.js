import { defineStore } from 'pinia'
import { ref } from 'vue'
import http from '@/lib/http'

export const useInstitutionStore = defineStore('institution', () => {
  const institutions    = ref([])
  const meta            = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0, per_page: 15 })
  const allInstitutions = ref([])   // untuk dropdown
  const loading         = ref(false)
  const errors          = ref({})

  async function fetchInstitutions(params = {}) {
    loading.value = true
    errors.value  = {}
    try {
      const { data } = await http.get('/admin/institutions', { params })
      institutions.value = data.data
      meta.value         = data.meta ?? meta.value
    } finally {
      loading.value = false
    }
  }

  async function fetchAllInstitutions() {
    try {
      const { data } = await http.get('/admin/institutions/all')
      allInstitutions.value = data.data
    } catch {}
  }

  async function createInstitution(payload) {
    errors.value = {}
    const { data } = await http.post('/admin/institutions', payload)
    return data
  }

  async function updateInstitution(id, payload) {
    errors.value = {}
    const { data } = await http.put(`/admin/institutions/${id}`, payload)
    return data
  }

  async function deleteInstitution(id) {
    const { data } = await http.delete(`/admin/institutions/${id}`)
    institutions.value = institutions.value.filter(i => i.id !== id)
    return data
  }

  async function restoreInstitution(id) {
    const { data } = await http.patch(`/admin/institutions/${id}/restore`)
    return data
  }

  function clearErrors() {
    errors.value = {}
  }

  return {
    institutions, meta, allInstitutions, loading, errors,
    fetchInstitutions, fetchAllInstitutions,
    createInstitution, updateInstitution,
    deleteInstitution, restoreInstitution,
    clearErrors,
  }
})
