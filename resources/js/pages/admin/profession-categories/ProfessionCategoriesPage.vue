<script setup>
import { ref, onMounted } from 'vue'
import { useProfessionCategoryStore } from '@/stores/useProfessionCategoryStore'
import AppTable from '@/components/base/AppTable.vue'
import AppPagination from '@/components/base/AppPagination.vue'
import AppButton from '@/components/base/AppButton.vue'
import AppBadge from '@/components/base/AppBadge.vue'
import AppConfirm from '@/components/base/AppConfirm.vue'
import ProfessionCategoryFormModal from './ProfessionCategoryFormModal.vue'

const store = useProfessionCategoryStore()

const showModal    = ref(false)
const showConfirm  = ref(false)
const editTarget   = ref(null)
const deleteTarget = ref(null)
const search       = ref('')

const columns = [
  { key: 'name',             label: 'Nama Kategori' },
  { key: 'description',      label: 'Deskripsi',   cellClass: 'max-w-xs truncate' },
  { key: 'professions_count', label: 'Profesi' },
  { key: 'is_active',        label: 'Status' },
  { key: 'actions',          label: 'Aksi', cellClass: 'text-right' },
]

onMounted(() => store.fetchProfessionCategories({ with_count: 'professions' }))

const onSearch      = () => store.fetchProfessionCategories({ search: search.value, page: 1, with_count: 'professions' })
const openCreate    = () => { editTarget.value = null; showModal.value = true }
const openEdit      = (row) => { editTarget.value = row; showModal.value = true }
const confirmDelete = (row) => { deleteTarget.value = row; showConfirm.value = true }
const doDelete      = async () => { await store.deleteProfessionCategory(deleteTarget.value.id); showConfirm.value = false }
const onSaved       = () => { showModal.value = false; store.fetchProfessionCategories({ with_count: 'professions' }) }
</script>

<template>
  <div class="space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Kategori Profesi</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Kelola kategori pengelompokan profesi alumni</p>
      </div>
      <AppButton variant="primary" size="sm" @click="openCreate">
        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Kategori
      </AppButton>
    </div>

    <!-- Search -->
    <div class="flex items-center gap-2 max-w-sm">
      <input
        v-model="search" type="text" placeholder="Cari nama kategori..."
        class="flex-1 px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600
               bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
               focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
        @keyup.enter="onSearch"
      />
      <AppButton variant="secondary" size="sm" @click="onSearch">Cari</AppButton>
    </div>

    <!-- Table -->
    <AppTable :columns="columns" :rows="store.professionCategories" :loading="store.loading" empty-text="Belum ada kategori profesi.">
      <template #cell-description="{ value }">
        <span class="text-gray-500 dark:text-gray-400 text-sm truncate">{{ value || '—' }}</span>
      </template>
      <template #cell-professions_count="{ value }">
        <span class="inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 rounded-full text-xs font-medium
                     bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">
          {{ value ?? 0 }}
        </span>
      </template>
      <template #cell-is_active="{ value }">
        <AppBadge :variant="value ? 'success' : 'danger'">{{ value ? 'Aktif' : 'Nonaktif' }}</AppBadge>
      </template>
      <template #cell-actions="{ row }">
        <div class="flex items-center justify-end gap-2">
          <button class="text-xs text-primary-600 hover:text-primary-800 font-medium" @click="openEdit(row)">Edit</button>
          <button class="text-xs text-red-500 hover:text-red-700 font-medium" @click="confirmDelete(row)">Hapus</button>
        </div>
      </template>
    </AppTable>

    <!-- Pagination -->
    <AppPagination
      v-if="store.meta.last_page > 1"
      :current-page="store.meta.current_page" :last-page="store.meta.last_page"
      :total="store.meta.total" :from="store.meta.from" :to="store.meta.to"
      @change="(p) => store.fetchProfessionCategories({ page: p, with_count: 'professions' })"
    />

    <!-- Modal & Confirm -->
    <ProfessionCategoryFormModal
      v-if="showModal"
      :category="editTarget"
      @close="showModal = false"
      @saved="onSaved"
    />
    <AppConfirm
      v-if="showConfirm"
      title="Hapus Kategori Profesi"
      :message="`Yakin ingin menghapus kategori '${deleteTarget?.name}'? Semua profesi di kategori ini akan terpengaruh.`"
      confirm-label="Hapus" variant="danger"
      @confirm="doDelete" @cancel="showConfirm = false"
    />
  </div>
</template>
