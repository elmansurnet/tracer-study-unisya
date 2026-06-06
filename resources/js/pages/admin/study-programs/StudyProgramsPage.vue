<script setup>
import { ref, onMounted } from 'vue'
import { useStudyProgramStore } from '@/stores/useStudyProgramStore'
import { useFacultyStore } from '@/stores/useFacultyStore'
import AppTable from '@/components/base/AppTable.vue'
import AppPagination from '@/components/base/AppPagination.vue'
import AppButton from '@/components/base/AppButton.vue'
import AppBadge from '@/components/base/AppBadge.vue'
import AppConfirm from '@/components/base/AppConfirm.vue'
import StudyProgramFormModal from './StudyProgramFormModal.vue'

const store   = useStudyProgramStore()
const fStore  = useFacultyStore()

const showModal    = ref(false)
const showConfirm  = ref(false)
const editTarget   = ref(null)
const deleteTarget = ref(null)
const search       = ref('')
const filterFaculty = ref('')

const columns = [
  { key: 'code',         label: 'Kode' },
  { key: 'name',         label: 'Nama Program Studi' },
  { key: 'degree',       label: 'Jenjang' },
  { key: 'faculty_name', label: 'Fakultas' },
  { key: 'is_active',    label: 'Status' },
  { key: 'actions',      label: 'Aksi', cellClass: 'text-right' },
]

onMounted(() => {
  store.fetchStudyPrograms()
  fStore.fetchAllFaculties()
})

const doSearch   = () => store.fetchStudyPrograms({ search: search.value, faculty_id: filterFaculty.value, page: 1 })
const openCreate = () => { editTarget.value = null; showModal.value = true }
const openEdit   = (row) => { editTarget.value = row; showModal.value = true }
const confirmDelete = (row) => { deleteTarget.value = row; showConfirm.value = true }
const doDelete   = async () => { await store.deleteStudyProgram(deleteTarget.value.id); showConfirm.value = false }
const onSaved    = () => { showModal.value = false; store.fetchStudyPrograms() }
</script>

<template>
  <div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Manajemen Program Studi</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data program studi UNISYA</p>
      </div>
      <AppButton variant="primary" size="sm" @click="openCreate">
        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Program Studi
      </AppButton>
    </div>

    <!-- Filter bar -->
    <div class="flex flex-wrap items-center gap-2">
      <input
        v-model="search" type="text" placeholder="Cari nama atau kode..."
        class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600
               bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100
               focus:outline-none focus:ring-2 focus:ring-primary-500 transition w-56"
        @keyup.enter="doSearch"
      />
      <select
        v-model="filterFaculty"
        class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600
               bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500"
        @change="doSearch"
      >
        <option value="">Semua Fakultas</option>
        <option v-for="f in fStore.allFaculties" :key="f.id" :value="f.id">{{ f.name }}</option>
      </select>
      <AppButton variant="secondary" size="sm" @click="doSearch">Cari</AppButton>
    </div>

    <AppTable :columns="columns" :rows="store.studyPrograms" :loading="store.loading" empty-text="Belum ada program studi.">
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
      @change="(p) => store.fetchStudyPrograms({ page: p })"
    />

    <StudyProgramFormModal v-if="showModal" :program="editTarget" :faculties="fStore.allFaculties" @close="showModal = false" @saved="onSaved" />
    <AppConfirm
      v-if="showConfirm" title="Hapus Program Studi"
      :message="`Yakin ingin menghapus program studi '${deleteTarget?.name}'?`"
      confirm-label="Hapus" variant="danger"
      @confirm="doDelete" @cancel="showConfirm = false"
    />
  </div>
</template>