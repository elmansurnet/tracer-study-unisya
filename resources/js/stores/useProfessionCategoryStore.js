import { defineStore } from 'pinia'
import { ref } from 'vue'
import http from '@/lib/http'

export const useProfessionCategoryStore = defineStore('professionCategory', () => {
  const professionCategories = ref([])
  const meta                 = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0, per_page: 15 })
  const allCategories        = ref([])   // untuk dropdown
  const loading              = ref(false)
  const errors               = ref({})

  async function fetchProfessionCategories(params = {}) {
    loading.value = true
    errors.value  = {}
    try {
      const { data } = await http.get('/admin/profession-categories', { params })
      professionCategories.value = data.data
      meta.value                 = data.meta ?? meta.value
    } finally {
      loading.value = false
    }
  }

  async function fetchAllCategories() {
    try {
      const { data } = await http.get('/admin/profession-categories/all')
      allCategories.value = data.data
    } catch {}
  }

  async function createProfessionCategory(payload) {
    errors.value = {}
    const { data } = await http.post('/admin/profession-categories', payload)
    return data
  }

  async function updateProfessionCategory(id, payload) {
    errors.value = {}
    const { data } = await http.put(`/admin/profession-categories/${id}`, payload)
    return data
  }

  async function deleteProfessionCategory(id) {
    const { data } = await http.delete(`/admin/profession-categories/${id}`)
    professionCategories.value = professionCategories.value.filter(c => c.id !== id)
    return data
  }

  async function restoreProfessionCategory(id) {
    const { data } = await http.patch(`/admin/profession-categories/${id}/restore`)
    return data
  }

  function clearErrors() {
    errors.value = {}
  }

  return {
    professionCategories, meta, allCategories, loading, errors,
    fetchProfessionCategories, fetchAllCategories,
    createProfessionCategory, updateProfessionCategory,
    deleteProfessionCategory, restoreProfessionCategory,
    clearErrors,
  }
})
