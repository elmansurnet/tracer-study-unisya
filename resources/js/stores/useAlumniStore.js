import { defineStore } from 'pinia'
import { ref } from 'vue'
import http from '@/lib/http'

export const useAlumniStore = defineStore('alumni', () => {
  // ── State ────────────────────────────────────────────────────────────────
  const alumni             = ref([])
  const meta               = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0, per_page: 15 })
  const currentAlumni      = ref(null)
  const employmentHistories = ref([])
  const employmentMeta     = ref({ current_page: 1, last_page: 1, total: 0 })
  const graduationYears    = ref([])
  const employmentStats    = ref(null)
  const loading            = ref(false)
  const errors             = ref({})

  // ── Alumni CRUD ──────────────────────────────────────────────────────────

  async function fetchAlumni(params = {}) {
    loading.value = true
    errors.value  = {}
    try {
      const { data } = await http.get('/admin/alumni', { params })
      alumni.value = data.data
      meta.value   = data.meta ?? meta.value
    } finally {
      loading.value = false
    }
  }

  async function fetchAlumniById(id) {
    loading.value = true
    errors.value  = {}
    try {
      const { data } = await http.get(`/admin/alumni/${id}`)
      currentAlumni.value = data.data
      return data.data
    } finally {
      loading.value = false
    }
  }

  async function fetchGraduationYears() {
    try {
      const { data } = await http.get('/admin/alumni/graduation-years')
      graduationYears.value = data.data
    } catch {}
  }

  async function fetchEmploymentStats() {
    try {
      const { data } = await http.get('/admin/alumni/employment-stats')
      employmentStats.value = data.data
    } catch {}
  }

  async function createAlumni(payload) {
    errors.value = {}
    const { data } = await http.post('/admin/alumni', payload)
    return data
  }

  async function updateAlumni(id, payload) {
    errors.value = {}
    const { data } = await http.put(`/admin/alumni/${id}`, payload)
    if (currentAlumni.value?.id === id) {
      currentAlumni.value = data.data
    }
    return data
  }

  async function deleteAlumni(id) {
    const { data } = await http.delete(`/admin/alumni/${id}`)
    alumni.value = alumni.value.filter(a => a.id !== id)
    return data
  }

  async function restoreAlumni(id) {
    const { data } = await http.patch(`/admin/alumni/${id}/restore`)
    return data
  }

  // ── Employment Histories (Admin nested route) ─────────────────────────────

  async function fetchEmploymentHistories(alumniId, params = {}) {
    loading.value = true
    try {
      const { data } = await http.get(`/admin/alumni/${alumniId}/employment-histories`, { params })
      employmentHistories.value = data.data
      employmentMeta.value      = data.meta ?? employmentMeta.value
    } finally {
      loading.value = false
    }
  }

  async function createEmploymentHistory(alumniId, payload) {
    errors.value = {}
    const { data } = await http.post(`/admin/alumni/${alumniId}/employment-histories`, payload)
    return data
  }

  async function updateEmploymentHistory(alumniId, historyId, payload) {
    errors.value = {}
    const { data } = await http.put(`/admin/alumni/${alumniId}/employment-histories/${historyId}`, payload)
    return data
  }

  async function deleteEmploymentHistory(alumniId, historyId) {
    const { data } = await http.delete(`/admin/alumni/${alumniId}/employment-histories/${historyId}`)
    employmentHistories.value = employmentHistories.value.filter(h => h.id !== historyId)
    return data
  }

  async function restoreEmploymentHistory(alumniId, historyId) {
    const { data } = await http.patch(`/admin/alumni/${alumniId}/employment-histories/${historyId}/restore`)
    return data
  }

  // ── Employment Histories (Alumni Self route) ──────────────────────────────

  async function fetchMyEmploymentHistories(params = {}) {
    loading.value = true
    try {
      const { data } = await http.get('/alumni/employment-histories', { params })
      employmentHistories.value = data.data
      employmentMeta.value      = data.meta ?? employmentMeta.value
    } finally {
      loading.value = false
    }
  }

  async function createMyEmploymentHistory(payload) {
    errors.value = {}
    const { data } = await http.post('/alumni/employment-histories', payload)
    return data
  }

  async function updateMyEmploymentHistory(historyId, payload) {
    errors.value = {}
    const { data } = await http.put(`/alumni/employment-histories/${historyId}`, payload)
    return data
  }

  async function deleteMyEmploymentHistory(historyId) {
    const { data } = await http.delete(`/alumni/employment-histories/${historyId}`)
    employmentHistories.value = employmentHistories.value.filter(h => h.id !== historyId)
    return data
  }

  async function restoreMyEmploymentHistory(historyId) {
    const { data } = await http.patch(`/alumni/employment-histories/${historyId}/restore`)
    return data
  }

  // ── Alumni Self (Profile) ─────────────────────────────────────────────────

  async function fetchMyProfile() {
    loading.value = true
    try {
      const { data } = await http.get('/alumni/profile')
      currentAlumni.value = data.data
      return data.data
    } finally {
      loading.value = false
    }
  }

  async function updateMyProfile(payload) {
    errors.value = {}
    const { data } = await http.put('/alumni/profile', payload)
    currentAlumni.value = data.data
    return data
  }

  async function updateMyEmploymentStatus(payload) {
    errors.value = {}
    const { data } = await http.patch('/alumni/profile/employment-status', payload)
    if (currentAlumni.value) {
      currentAlumni.value.is_employed          = data.data.is_employed
      currentAlumni.value.waiting_period_months = data.data.waiting_period_months
    }
    return data
  }

  // ── Autocomplete search (untuk dropdown institusi/profesi di form lain) ───

  async function searchInstitutions(query) {
    if (!query || query.length < 2) return []
    try {
      const { data } = await http.get('/admin/institutions', {
        params: { search: query, per_page: 10 },
      })
      return data.data ?? []
    } catch {
      return []
    }
  }

  async function searchProfessions(query) {
    if (!query || query.length < 2) return []
    try {
      const { data } = await http.get('/admin/professions', {
        params: { search: query, per_page: 10 },
      })
      return data.data ?? []
    } catch {
      return []
    }
  }

  // ── Alumni self: autocomplete (route tanpa /admin prefix) ─────────────────

  async function searchInstitutionsPublic(query) {
    if (!query || query.length < 2) return []
    try {
      const { data } = await http.get('/alumni/institutions/search', {
        params: { q: query },
      })
      return data.data ?? []
    } catch {
      return []
    }
  }

  async function searchProfessionsPublic(query) {
    if (!query || query.length < 2) return []
    try {
      const { data } = await http.get('/alumni/professions/search', {
        params: { q: query },
      })
      return data.data ?? []
    } catch {
      return []
    }
  }

  // ── Helpers ───────────────────────────────────────────────────────────────

  function clearErrors() {
    errors.value = {}
  }

  function clearCurrentAlumni() {
    currentAlumni.value = null
  }

  function clearEmploymentHistories() {
    employmentHistories.value = []
  }

  return {
    // state
    alumni, meta, currentAlumni,
    employmentHistories, employmentMeta,
    graduationYears, employmentStats,
    loading, errors,
    // admin alumni
    fetchAlumni, fetchAlumniById,
    fetchGraduationYears, fetchEmploymentStats,
    createAlumni, updateAlumni, deleteAlumni, restoreAlumni,
    // admin employment
    fetchEmploymentHistories,
    createEmploymentHistory, updateEmploymentHistory,
    deleteEmploymentHistory, restoreEmploymentHistory,
    // alumni self
    fetchMyProfile, updateMyProfile, updateMyEmploymentStatus,
    fetchMyEmploymentHistories,
    createMyEmploymentHistory, updateMyEmploymentHistory,
    deleteMyEmploymentHistory, restoreMyEmploymentHistory,
    // autocomplete
    searchInstitutions, searchProfessions,
    searchInstitutionsPublic, searchProfessionsPublic,
    // helpers
    clearErrors, clearCurrentAlumni, clearEmploymentHistories,
  }
})
