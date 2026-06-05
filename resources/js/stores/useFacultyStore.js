import { defineStore } from 'pinia'
import { ref } from 'vue'
import http from '@/lib/http'

export const useFacultyStore = defineStore('faculty', () => {
  const faculties   = ref([])
  const pagination  = ref({})
  const allFaculties = ref([])   // untuk dropdown
  const loading     = ref(false)
  const errors      = ref({})

  // ─ List (paginasi) ───────────────────────────────────────────────
  async function fetchFaculties(params = {}) {
    loading.value = true
    errors.value  = {}
    try {
      const { data } = await http.get('/admin/faculties', { params })
      faculties.value  = data.data
      pagination.value = data.meta ?? {}
    } finally {
      loading.value = false
    }
  }

  // ─ Dropdown (all active) ─────────────────────────────────────
  async function fetchAllFaculties() {
    try {
      const { data } = await http.get('/admin/faculties/all')
      allFaculties.value = data.data
    } catch {}
  }

  // ─ Create ──────────────────────────────────────────────────────
  async function createFaculty(payload) {
    errors.value = {}
    const { data } = await http.post('/admin/faculties', payload)
    return data
  }

  // ─ Update ──────────────────────────────────────────────────────
  async function updateFaculty(id, payload) {
    errors.value = {}
    const { data } = await http.put(`/admin/faculties/${id}`, payload)
    return data
  }

  // ─ Delete ──────────────────────────────────────────────────────
  async function deleteFaculty(id) {
    const { data } = await http.delete(`/admin/faculties/${id}`)
    return data
  }

  // ─ Restore ────────────────────────────────────────────────────
  async function restoreFaculty(id) {
    const { data } = await http.patch(`/admin/faculties/${id}/restore`)
    return data
  }

  function clearErrors() {
    errors.value = {}
  }

  return {
    faculties, pagination, allFaculties, loading, errors,
    fetchFaculties, fetchAllFaculties,
    createFaculty, updateFaculty, deleteFaculty, restoreFaculty,
    clearErrors,
  }
})
