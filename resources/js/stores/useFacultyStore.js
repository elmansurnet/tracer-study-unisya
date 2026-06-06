import { defineStore } from 'pinia'
import { ref } from 'vue'
import http from '@/lib/http'

export const useFacultyStore = defineStore('faculty', () => {
  const faculties    = ref([])
  const meta         = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0, per_page: 15 })
  const allFaculties = ref([])   // untuk dropdown
  const loading      = ref(false)
  const errors       = ref({})

  async function fetchFaculties(params = {}) {
    loading.value = true
    errors.value  = {}
    try {
      const { data } = await http.get('/admin/faculties', { params })
      faculties.value = data.data
      meta.value      = data.meta ?? meta.value
    } finally {
      loading.value = false
    }
  }

  async function fetchAllFaculties() {
    try {
      const { data } = await http.get('/admin/faculties/all')
      allFaculties.value = data.data
    } catch {}
  }

  async function createFaculty(payload) {
    errors.value = {}
    const { data } = await http.post('/admin/faculties', payload)
    return data
  }

  async function updateFaculty(id, payload) {
    errors.value = {}
    const { data } = await http.put(`/admin/faculties/${id}`, payload)
    return data
  }

  async function deleteFaculty(id) {
    const { data } = await http.delete(`/admin/faculties/${id}`)
    faculties.value = faculties.value.filter(f => f.id !== id)
    return data
  }

  async function restoreFaculty(id) {
    const { data } = await http.patch(`/admin/faculties/${id}/restore`)
    return data
  }

  function clearErrors() {
    errors.value = {}
  }

  return {
    faculties, meta, allFaculties, loading, errors,
    fetchFaculties, fetchAllFaculties,
    createFaculty, updateFaculty, deleteFaculty, restoreFaculty,
    clearErrors,
  }
})
