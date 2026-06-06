<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-xl font-semibold text-gray-900">Manajemen Alumni</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola data alumni seluruh program studi</p>
      </div>
      <div class="flex items-center gap-2">
        <ImportModal @imported="onImported" />
        <ExportButton />
        <button
          class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
          @click="openCreateModal"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          Tambah Alumni
        </button>
      </div>
    </div>

    <!-- Stats Bar -->
    <div v-if="store.employmentStats" class="grid grid-cols-2 gap-3 sm:grid-cols-4">
      <div class="rounded-lg border border-gray-200 bg-white px-4 py-3">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Alumni</p>
        <p class="mt-1 text-2xl font-bold text-gray-900">{{ store.meta.total }}</p>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white px-4 py-3">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Bekerja</p>
        <p class="mt-1 text-2xl font-bold text-green-600">{{ store.employmentStats.employed ?? 0 }}</p>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white px-4 py-3">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Wirausaha</p>
        <p class="mt-1 text-2xl font-bold text-blue-600">{{ store.employmentStats.self_employed ?? 0 }}</p>
      </div>
      <div class="rounded-lg border border-gray-200 bg-white px-4 py-3">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Belum Bekerja</p>
        <p class="mt-1 text-2xl font-bold text-orange-500">{{ store.employmentStats.unemployed ?? 0 }}</p>
      </div>
    </div>

    <!-- Filter Panel -->
    <div class="rounded-lg border border-gray-200 bg-white p-4">
      <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
        <!-- Search -->
        <div class="lg:col-span-2">
          <label class="block text-xs font-medium text-gray-700 mb-1">Cari (Nama / NIM)</label>
          <div class="relative">
            <svg class="absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
            </svg>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Nama atau NIM..."
              class="w-full rounded-md border border-gray-300 py-2 pl-8 pr-3 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
              @keyup.enter="applyFilters"
            />
          </div>
        </div>
        <!-- Fakultas -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Fakultas</label>
          <select
            v-model="filters.faculty_id"
            class="w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            @change="onFacultyChange"
          >
            <option value="">Semua Fakultas</option>
            <option v-for="f in faculties" :key="f.id" :value="f.id">{{ f.name }}</option>
          </select>
        </div>
        <!-- Program Studi -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Program Studi</label>
          <select
            v-model="filters.study_program_id"
            class="w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            :disabled="!filters.faculty_id"
          >
            <option value="">Semua Prodi</option>
            <option v-for="sp in filteredStudyPrograms" :key="sp.id" :value="sp.id">{{ sp.name }}</option>
          </select>
        </div>
        <!-- Tahun Lulus -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Tahun Lulus</label>
          <select
            v-model="filters.graduation_year"
            class="w-full rounded-md border border-gray-300 py-2 px-3 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
          >
            <option value="">Semua Tahun</option>
            <option v-for="y in store.graduationYears" :key="y" :value="y">{{ y }}</option>
          </select>
        </div>
      </div>
      <div class="mt-3 flex items-center gap-2">
        <!-- Status Kerja -->
        <div class="flex items-center gap-1">
          <label class="text-xs font-medium text-gray-700">Status:</label>
          <select
            v-model="filters.employment_status"
            class="rounded-md border border-gray-300 py-1.5 px-2 text-xs focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
          >
            <option value="">Semua</option>
            <option value="employed">Bekerja</option>
            <option value="self_employed">Wirausaha</option>
            <option value="unemployed">Belum Bekerja</option>
            <option value="continuing_study">Lanjut Studi</option>
          </select>
        </div>
        <!-- Termasuk Dihapus -->
        <label class="flex items-center gap-1.5 text-xs text-gray-600">
          <input v-model="filters.with_trashed" type="checkbox" class="rounded border-gray-300" />
          Tampilkan yang dihapus
        </label>
        <button
          class="ml-auto rounded-md bg-primary-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-primary-700"
          @click="applyFilters"
        >
          Terapkan Filter
        </button>
        <button
          class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50"
          @click="resetFilters"
        >
          Reset
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="store.loading" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="i in 6" :key="i" class="h-40 animate-pulse rounded-lg bg-gray-100" />
    </div>

    <!-- Empty -->
    <div v-else-if="!store.alumni.length" class="flex flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white py-16">
      <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
      </svg>
      <p class="mt-3 text-sm font-medium text-gray-900">Tidak ada alumni ditemukan</p>
      <p class="mt-1 text-xs text-gray-500">Coba ubah filter atau tambah alumni baru.</p>
      <button class="mt-4 rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700" @click="openCreateModal">Tambah Alumni Pertama</button>
    </div>

    <!-- Grid Kartu Alumni -->
    <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <AlumniCard
        v-for="alum in store.alumni"
        :key="alum.id"
        :alumni="alum"
        @click="goToDetail(alum.id)"
      >
        <template #actions>
          <div class="flex items-center gap-1">
            <button
              class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-primary-600"
              title="Lihat Detail"
              @click.stop="goToDetail(alum.id)"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
              </svg>
            </button>
            <button
              class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-blue-600"
              title="Edit Alumni"
              @click.stop="openEditModal(alum)"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.1 2.1 0 1 1 2.97 2.97L8.88 18.41l-4 1 1-4 11.982-10.923Z" />
              </svg>
            </button>
            <button
              v-if="!alum.deleted_at"
              class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-red-600"
              title="Hapus Alumni"
              @click.stop="confirmDelete(alum)"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16" />
              </svg>
            </button>
            <button
              v-else
              class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-green-600"
              title="Pulihkan Alumni"
              @click.stop="handleRestore(alum.id)"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
              </svg>
            </button>
          </div>
        </template>
      </AlumniCard>
    </div>

    <!-- Pagination -->
    <div v-if="store.meta.last_page > 1" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 rounded-lg">
      <p class="text-sm text-gray-600">
        Menampilkan <span class="font-medium">{{ store.meta.from }}</span>–<span class="font-medium">{{ store.meta.to }}</span>
        dari <span class="font-medium">{{ store.meta.total }}</span> alumni
      </p>
      <div class="flex items-center gap-1">
        <button
          :disabled="store.meta.current_page === 1"
          class="rounded-md border border-gray-300 px-3 py-1.5 text-sm disabled:cursor-not-allowed disabled:opacity-40 hover:bg-gray-50"
          @click="goToPage(store.meta.current_page - 1)"
        >
          &lsaquo; Prev
        </button>
        <button
          v-for="p in visiblePages"
          :key="p"
          :class="[
            'rounded-md border px-3 py-1.5 text-sm',
            p === store.meta.current_page
              ? 'border-primary-500 bg-primary-50 text-primary-700 font-medium'
              : 'border-gray-300 hover:bg-gray-50',
          ]"
          @click="goToPage(p)"
        >
          {{ p }}
        </button>
        <button
          :disabled="store.meta.current_page === store.meta.last_page"
          class="rounded-md border border-gray-300 px-3 py-1.5 text-sm disabled:cursor-not-allowed disabled:opacity-40 hover:bg-gray-50"
          @click="goToPage(store.meta.current_page + 1)"
        >
          Next &rsaquo;
        </button>
      </div>
    </div>

    <!-- Modal Form Alumni (Create / Edit) -->
    <AlumniFormStepper
      v-if="showFormModal"
      :alumni="selectedAlumni"
      @close="showFormModal = false"
      @saved="onAlumniSaved"
    />

    <!-- Confirm Delete Dialog -->
    <div v-if="showDeleteDialog" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="w-full max-w-sm rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-base font-semibold text-gray-900">Hapus Alumni?</h3>
        <p class="mt-2 text-sm text-gray-600">
          Alumni <span class="font-medium">{{ deleteTarget?.name }}</span> ({{ deleteTarget?.nim }}) akan dihapus sementara (soft delete).
          Data dapat dipulihkan kembali.
        </p>
        <div class="mt-4 flex justify-end gap-2">
          <button class="rounded-lg border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50" @click="showDeleteDialog = false">Batal</button>
          <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700" :disabled="deleteLoading" @click="handleDelete">
            {{ deleteLoading ? 'Menghapus...' : 'Hapus' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAlumniStore } from '@/stores/useAlumniStore'
import AlumniCard from '@/components/alumni/AlumniCard.vue'
import AlumniFormStepper from '@/pages/admin/alumni/AlumniFormStepper.vue'
import ImportModal from '@/components/alumni/ImportModal.vue'
import ExportButton from '@/components/alumni/ExportButton.vue'
import http from '@/lib/http'

const router = useRouter()
const store  = useAlumniStore()

// ── Filter State ─────────────────────────────────────────────────────────────
const filters = reactive({
  search:            '',
  faculty_id:        '',
  study_program_id:  '',
  graduation_year:   '',
  employment_status: '',
  with_trashed:      false,
  page:              1,
  per_page:          15,
})

// ── Supporting Data ──────────────────────────────────────────────────────────
const faculties         = ref([])
const studyPrograms     = ref([])

const filteredStudyPrograms = computed(() =>
  filters.faculty_id
    ? studyPrograms.value.filter(sp => sp.faculty_id === filters.faculty_id)
    : studyPrograms.value
)

const visiblePages = computed(() => {
  const total   = store.meta.last_page
  const current = store.meta.current_page
  const delta   = 2
  const pages   = []
  for (let i = Math.max(1, current - delta); i <= Math.min(total, current + delta); i++) pages.push(i)
  return pages
})

// ── Modal State ───────────────────────────────────────────────────────────────
const showFormModal   = ref(false)
const selectedAlumni  = ref(null)
const showDeleteDialog = ref(false)
const deleteTarget    = ref(null)
const deleteLoading   = ref(false)

// ── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(async () => {
  await Promise.all([
    store.fetchAlumni(buildParams()),
    store.fetchGraduationYears(),
    store.fetchEmploymentStats(),
    loadFaculties(),
    loadStudyPrograms(),
  ])
})

async function loadFaculties() {
  try {
    const { data } = await http.get('/admin/faculties', { params: { per_page: 100 } })
    faculties.value = data.data ?? []
  } catch {}
}

async function loadStudyPrograms() {
  try {
    const { data } = await http.get('/admin/study-programs', { params: { per_page: 200 } })
    studyPrograms.value = data.data ?? []
  } catch {}
}

// ── Filter Actions ────────────────────────────────────────────────────────────
function buildParams() {
  const p = { page: filters.page, per_page: filters.per_page }
  if (filters.search)            p.search            = filters.search
  if (filters.faculty_id)        p.faculty_id        = filters.faculty_id
  if (filters.study_program_id)  p.study_program_id  = filters.study_program_id
  if (filters.graduation_year)   p.graduation_year   = filters.graduation_year
  if (filters.employment_status) p.employment_status = filters.employment_status
  if (filters.with_trashed)      p.with_trashed      = '1'
  return p
}

function applyFilters() {
  filters.page = 1
  store.fetchAlumni(buildParams())
}

function resetFilters() {
  filters.search            = ''
  filters.faculty_id        = ''
  filters.study_program_id  = ''
  filters.graduation_year   = ''
  filters.employment_status = ''
  filters.with_trashed      = false
  filters.page              = 1
  store.fetchAlumni(buildParams())
}

function onFacultyChange() {
  filters.study_program_id = ''
}

function goToPage(page) {
  filters.page = page
  store.fetchAlumni(buildParams())
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// ── Navigation ────────────────────────────────────────────────────────────────
function goToDetail(id) {
  router.push({ name: 'admin-alumni-detail', params: { id } })
}

// ── Create / Edit ─────────────────────────────────────────────────────────────
function openCreateModal() {
  selectedAlumni.value = null
  showFormModal.value  = true
}

function openEditModal(alum) {
  selectedAlumni.value = alum
  showFormModal.value  = true
}

function onAlumniSaved() {
  showFormModal.value = false
  store.fetchAlumni(buildParams())
  store.fetchEmploymentStats()
}

function onImported() {
  store.fetchAlumni(buildParams())
  store.fetchEmploymentStats()
}

// ── Delete / Restore ──────────────────────────────────────────────────────────
function confirmDelete(alum) {
  deleteTarget.value    = alum
  showDeleteDialog.value = true
}

async function handleDelete() {
  if (!deleteTarget.value) return
  deleteLoading.value = true
  try {
    await store.deleteAlumni(deleteTarget.value.id)
    showDeleteDialog.value = false
    deleteTarget.value     = null
    store.fetchAlumni(buildParams())
    store.fetchEmploymentStats()
  } finally {
    deleteLoading.value = false
  }
}

async function handleRestore(id) {
  await store.restoreAlumni(id)
  store.fetchAlumni(buildParams())
  store.fetchEmploymentStats()
}
</script>
