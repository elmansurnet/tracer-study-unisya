import { defineStore } from 'pinia'
import { ref } from 'vue'
import http from '@/lib/http'

export const useStudyProgramStore = defineStore('studyProgram', () => {
  const programs     = ref([])
  const pagination   = ref({})
  const allPrograms  = ref([])   // untuk dropdown
  const loading      = ref(false)
  const errors       = ref({})

  async function fetchPrograms(params = {}) {
    loading.value = true
    errors.value  = {}
    try {
      const { data } = await http.get('/admin/study-programs', { params })
      programs.value   = data.data
      pagination.value = data.meta ?? {}
    } finally {
      loading.value = false
    }
  }

  async function fetchAllPrograms(facultyId = null) {
    try {
      const params = facultyId ? { faculty_id: facultyId } : {}
      const { data } = await http.get('/admin/study-programs/all', { params })
      allPrograms.value = data.data
    } catch {}
  }

  async function createProgram(payload) {
    errors.value = {}
    const { data } = await http.post('/admin/study-programs', payload)
    return data
  }

  async function updateProgram(id, payload) {
    errors.value = {}
    const { data } = await http.put(`/admin/study-programs/${id}`, payload)
    return data
  }

  async function deleteProgram(id) {
    const { data } = await http.delete(`/admin/study-programs/${id}`)
    return data
  }

  async function restoreProgram(id) {
    const { data } = await http.patch(`/admin/study-programs/${id}/restore`)
    return data
  }

  function clearErrors() {
    errors.value = {}
  }

  return {
    programs, pagination, allPrograms, loading, errors,
    fetchPrograms, fetchAllPrograms,
    createProgram, updateProgram, deleteProgram, restoreProgram,
    clearErrors,
  }
})
