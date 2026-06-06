<script setup>
import { ref, onMounted } from 'vue'
import { useFacultyStore } from '@/stores/useFacultyStore'
import AppTable from '@/components/base/AppTable.vue'
import AppPagination from '@/components/base/AppPagination.vue'
import AppButton from '@/components/base/AppButton.vue'
import AppBadge from '@/components/base/AppBadge.vue'
import AppConfirm from '@/components/base/AppConfirm.vue'
import FacultyFormModal from './FacultyFormModal.vue'

const store = useFacultyStore()

const showModal    = ref(false)
const showConfirm  = ref(false)
const editTarget   = ref(null)
const deleteTarget = ref(null)
const search       = ref('')

const columns = [
  { key: 'code',      label: 'Kode' },
  { key: 'name',      label: 'Nama Fakultas' },
  { key: 'study_programs_count', label: 'Prodi' },
  { key: 'is_active', label: 'Status' },
  { key: 'actions',   label: 'Aksi', cellClass: 'text-right' },
]

onMounted(() => store.fetchFaculties())

const onSearch     = () => store.fetchFaculties({ search: search.value, page: 1 })
const openCreate   = () => { editTarget.value = null; showModal.value = true }
const openEdit     = (row) => { editTarget.value = row; showModal.value = true }
const confirmDelete = (row) => { deleteTarget.value = row; showConfirm.value = true }
const doDelete     = async () => { await store.deleteFaculty(deleteTarget.value.id); showConfirm.value = false }
const onSaved      = () => { showModal.value = false; store.fetchFaculties() }
</script>

<template>
  <div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Manajemen Fakultas</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data fakultas UNISYA</p>
      </div>
      <AppButton variant="primary" size="sm" @click="openCreate">
        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Fakultas
      </AppButton>
    </div>

    <div class="flex items-center gap-2 max-w-sm">
      <input
        v-model="search" type="text" placeholder="Cari nama atau kode..."
        class="flex-1 px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600
               bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
               focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
        @keyup.enter="onSearch"
      />
      <AppButton variant="secondary" size="sm" @click="onSearch">Cari</AppButton>
    </div>

    <AppTable :columns="columns" :rows="store.faculties" :loading="store.loading" empty-text="Belum ada fakultas.">
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

    <AppPagination
      v-if="store.meta.last_page > 1"
      :current-page="store.meta.current_page" :last-page="store.meta.last_page"
      :total="store.meta.total" :from="store.meta.from" :to="store.meta.to"
      @change="(p) => store.fetchFaculties({ page: p })"
    />

    <FacultyFormModal v-if="showModal" :faculty="editTarget" @close="showModal = false" @saved="onSaved" />
    <AppConfirm
      v-if="showConfirm" title="Hapus Fakultas"
      :message="`Yakin ingin menghapus fakultas '${deleteTarget?.name}'?`"
      confirm-label="Hapus" variant="danger"
      @confirm="doDelete" @cancel="showConfirm = false"
    />
  </div>
</template>