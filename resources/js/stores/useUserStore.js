import { defineStore } from 'pinia'
import { ref } from 'vue'
import http from '@/lib/http'

export const useUserStore = defineStore('user', () => {
  const users   = ref([])
  const meta    = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0, per_page: 15 })
  const loading = ref(false)
  const errors  = ref({})

  async function fetchUsers(params = {}) {
    loading.value = true
    errors.value  = {}
    try {
      const { data } = await http.get('/admin/users', { params })
      users.value = data.data
      meta.value  = data.meta ?? meta.value
    } finally {
      loading.value = false
    }
  }

  async function createUser(payload) {
    errors.value = {}
    const { data } = await http.post('/admin/users', payload)
    return data
  }

  async function updateUser(id, payload) {
    errors.value = {}
    const { data } = await http.put(`/admin/users/${id}`, payload)
    return data
  }

  async function deleteUser(id) {
    const { data } = await http.delete(`/admin/users/${id}`)
    return data
  }

  async function toggleActive(id) {
    const { data } = await http.patch(`/admin/users/${id}/toggle-active`)
    const idx = users.value.findIndex(u => u.id === id)
    if (idx !== -1) users.value[idx].is_active = !users.value[idx].is_active
    return data
  }

  async function resetPassword(id, payload) {
    const { data } = await http.post(`/admin/users/${id}/reset-password`, payload)
    return data
  }

  function clearErrors() {
    errors.value = {}
  }

  return {
    users, meta, loading, errors,
    fetchUsers, createUser, updateUser, deleteUser,
    toggleActive, resetPassword, clearErrors,
  }
})
