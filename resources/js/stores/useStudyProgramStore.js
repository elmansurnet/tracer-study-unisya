import { defineStore } from 'pinia'
import { ref } from 'vue'
import http from '@/lib/http'

export const useStudyProgramStore = defineStore('studyProgram', () => {
  const studyPrograms = ref([])
  const meta          = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0, per_page: 15 })
  const allPrograms   = ref([])   // untuk dropdown
  const loading       = ref(false)
  const errors        = ref({})

  async function fetchStudyPrograms(params = {}) {
    loading.value = true
    errors.value  = {}
    try {
      const { data } = await http.get('/admin/study-programs', { params })
      studyPrograms.value = data.data
      meta.value          = data.meta ?? meta.value
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

  async function createStudyProgram(payload) {
    errors.value = {}
    const { data } = await http.post('/admin/study-programs', payload)
    return data
  }

  async function updateStudyProgram(id, payload) {
    errors.value = {}
    const { data } = await http.put(`/admin/study-programs/${id}`, payload)
    return data
  }

  async function deleteStudyProgram(id) {
    const { data } = await http.delete(`/admin/study-programs/${id}`)
    studyPrograms.value = studyPrograms.value.filter(p => p.id !== id)
    return data
  }

  async function restoreStudyProgram(id) {
    const { data } = await http.patch(`/admin/study-programs/${id}/restore`)
    return data
  }

  function clearErrors() {
    errors.value = {}
  }

  return {
    studyPrograms, meta, allPrograms, loading, errors,
    fetchStudyPrograms, fetchAllPrograms,
    createStudyProgram, updateStudyProgram, deleteStudyProgram, restoreStudyProgram,
    clearErrors,
  }
})
