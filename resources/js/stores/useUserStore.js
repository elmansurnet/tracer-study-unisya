import { defineStore } from 'pinia'
import { ref } from 'vue'
import http from '@/lib/http'

export const useUserStore = defineStore('user', () => {
  const users      = ref([])
  const pagination = ref({})
  const loading    = ref(false)
  const errors     = ref({})

  async function fetchUsers(params = {}) {
    loading.value = true
    errors.value  = {}
    try {
      const { data } = await http.get('/admin/users', { params })
      users.value      = data.data
      pagination.value = data.meta ?? {}
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
    users, pagination, loading, errors,
    fetchUsers, createUser, updateUser, deleteUser,
    toggleActive, resetPassword, clearErrors,
  }
})
