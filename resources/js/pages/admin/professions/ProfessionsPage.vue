<script setup>
import { ref, onMounted } from 'vue'
import { useProfessionStore } from '@/stores/useProfessionStore'
import { useProfessionCategoryStore } from '@/stores/useProfessionCategoryStore'
import AppTable from '@/components/base/AppTable.vue'
import AppPagination from '@/components/base/AppPagination.vue'
import AppButton from '@/components/base/AppButton.vue'
import AppBadge from '@/components/base/AppBadge.vue'
import AppConfirm from '@/components/base/AppConfirm.vue'
import ProfessionFormModal from './ProfessionFormModal.vue'

const store    = useProfessionStore()
const catStore = useProfessionCategoryStore()

const showModal    = ref(false)
const showConfirm  = ref(false)
const editTarget   = ref(null)
const deleteTarget = ref(null)
const search       = ref('')
const filterCatId  = ref('')

const columns = [
  { key: 'name',     label: 'Nama Profesi' },
  { key: 'category', label: 'Kategori' },
  { key: 'description', label: 'Deskripsi', cellClass: 'max-w-xs truncate' },
  { key: 'is_active', label: 'Status' },
  { key: 'actions',  label: 'Aksi', cellClass: 'text-right' },
]

onMounted(() => {
  store.fetchProfessions()
  catStore.fetchAllCategories()
})

const buildParams = () => ({
  search: search.value || undefined,
  profession_category_id: filterCatId.value || undefined,
  page: 1,
})

const onSearch      = () => store.fetchProfessions(buildParams())
const openCreate    = () => { editTarget.value = null; showModal.value = true }
const openEdit      = (row) => { editTarget.value = row; showModal.value = true }
const confirmDelete = (row) => { deleteTarget.value = row; showConfirm.value = true }
const doDelete      = async () => { await store.deleteProfession(deleteTarget.value.id); showConfirm.value = false }
const onSaved       = () => { showModal.value = false; store.fetchProfessions() }
</script>

<template>
  <div class="space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Manajemen Profesi</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data profesi alumni berdasarkan kategori</p>
      </div>
      <AppButton variant="primary" size="sm" @click="openCreate">
        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Profesi
      </AppButton>
    </div>

    <!-- Filter & Search -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
      <input
        v-model="search" type="text" placeholder="Cari nama profesi..."
        class="w-full sm:max-w-xs px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600
               bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
               focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
        @keyup.enter="onSearch"
      />
      <select
        v-model="filterCatId"
        class="w-full sm:w-auto px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600
               bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
               focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
        @change="onSearch"
      >
        <option value="">Semua Kategori</option>
        <option v-for="cat in catStore.allCategories" :key="cat.id" :value="cat.id">
          {{ cat.name }}
        </option>
      </select>
      <AppButton variant="secondary" size="sm" @click="onSearch">Cari</AppButton>
    </div>

    <!-- Table -->
    <AppTable :columns="columns" :rows="store.professions" :loading="store.loading" empty-text="Belum ada profesi.">
      <template #cell-category="{ row }">
        <span class="text-sm text-gray-600 dark:text-gray-400">{{ row.category?.name ?? '—' }}</span>
      </template>
      <template #cell-description="{ value }">
        <span class="text-gray-500 dark:text-gray-400 text-sm">{{ value || '—' }}</span>
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
      @change="(p) => store.fetchProfessions({ page: p })" 
    />

    <!-- Modal & Confirm -->
    <ProfessionFormModal
      v-if="showModal"
      :profession="editTarget"
      @close="showModal = false"
      @saved="onSaved"
    />
    <AppConfirm
      v-if="showConfirm"
      title="Hapus Profesi"
      :message="`Yakin ingin menghapus profesi '${deleteTarget?.name}'?`"
      confirm-label="Hapus" variant="danger"
      @confirm="doDelete" @cancel="showConfirm = false"
    />
  </div>
</template>
