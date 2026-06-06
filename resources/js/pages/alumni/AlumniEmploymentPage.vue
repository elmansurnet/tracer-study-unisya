<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-xl font-semibold text-gray-900">Riwayat Pekerjaan</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola data pekerjaan Anda</p>
      </div>
      <button
        class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700"
        @click="openCreateForm"
      >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Pekerjaan
      </button>
    </div>

    <!-- Loading -->
    <div v-if="store.loading" class="space-y-3">
      <div v-for="i in 3" :key="i" class="h-28 animate-pulse rounded-xl bg-gray-100" />
    </div>

    <!-- Empty -->
    <div
      v-else-if="!store.employmentHistories.length"
      class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-white py-14"
    >
      <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0" />
      </svg>
      <p class="mt-3 text-sm font-medium text-gray-900">Belum ada riwayat pekerjaan</p>
      <p class="mt-1 text-xs text-gray-500">Tambahkan pekerjaan pertama Anda.</p>
      <button class="mt-4 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700" @click="openCreateForm">Tambah Sekarang</button>
    </div>

    <!-- List Pekerjaan -->
    <div v-else class="space-y-3">
      <div
        v-for="h in store.employmentHistories"
        :key="h.id"
        :class="[
          'rounded-xl border bg-white p-4 transition',
          h.deleted_at ? 'border-red-200 opacity-60' : 'border-gray-200',
          h.is_current  ? 'ring-2 ring-primary-400 ring-offset-1' : '',
        ]"
      >
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1 min-w-0">
            <!-- Badge Pekerjaan Saat Ini -->
            <span v-if="h.is_current" class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-700 mb-1">
              ✓ Pekerjaan Saat Ini
            </span>
            <span v-if="h.deleted_at" class="ml-1 inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-600">
              Dihapus
            </span>
            <p class="font-semibold text-gray-900 truncate">{{ h.job_title ?? '—' }}</p>
            <p class="text-sm text-gray-600 mt-0.5">
              {{ h.institution?.name ?? h.company_name ?? '—' }}
              <span v-if="h.institution?.city" class="text-gray-400"> · {{ h.institution.city }}</span>
            </p>
            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
              <span>{{ formatDate(h.start_date) }} — {{ h.end_date ? formatDate(h.end_date) : 'Sekarang' }}</span>
              <span v-if="h.profession?.name" class="inline-flex items-center rounded bg-gray-100 px-1.5 py-0.5 text-gray-600">
                {{ h.profession.name }}
              </span>
              <span v-if="h.employment_type" class="capitalize">{{ h.employment_type }}</span>
            </div>
            <p v-if="h.description" class="mt-1.5 text-xs text-gray-500 line-clamp-2">{{ h.description }}</p>
          </div>
          <!-- Action Buttons -->
          <div class="flex shrink-0 items-center gap-1">
            <button
              v-if="!h.deleted_at"
              class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-blue-600"
              title="Edit"
              @click="openEditForm(h)"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.1 2.1 0 1 1 2.97 2.97L8.88 18.41l-4 1 1-4 11.982-10.923Z" />
              </svg>
            </button>
            <button
              v-if="!h.deleted_at"
              class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-red-600"
              title="Hapus"
              @click="confirmDelete(h)"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16" />
              </svg>
            </button>
            <button
              v-else
              class="rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-green-600"
              title="Pulihkan"
              @click="handleRestore(h.id)"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Modal (Create / Edit) -->
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div class="w-full max-w-lg rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
          <h3 class="text-base font-semibold text-gray-900">
            {{ editTarget ? 'Edit Pekerjaan' : 'Tambah Pekerjaan' }}
          </h3>
          <button class="rounded p-1 text-gray-400 hover:bg-gray-100" @click="closeForm">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <form class="space-y-4 p-5" @submit.prevent="submitForm">
          <!-- Institusi (Autocomplete) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Institusi / Perusahaan *</label>
            <div class="relative">
              <input
                v-model="formData.institution_search"
                type="text"
                placeholder="Ketik nama institusi..."
                autocomplete="off"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                @input="onInstitutionSearch"
                @focus="showInstSuggestions = true"
              />
              <ul
                v-if="showInstSuggestions && instSuggestions.length"
                class="absolute left-0 right-0 top-full z-10 mt-1 max-h-48 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg"
              >
                <li
                  v-for="inst in instSuggestions"
                  :key="inst.id"
                  class="cursor-pointer px-3 py-2 text-sm hover:bg-primary-50"
                  @mousedown.prevent="selectInstitution(inst)"
                >
                  <span class="font-medium">{{ inst.name }}</span>
                  <span v-if="inst.city" class="ml-1 text-gray-400">· {{ inst.city }}</span>
                </li>
              </ul>
            </div>
            <p v-if="formErrors.institution_id" class="mt-1 text-xs text-red-500">{{ formErrors.institution_id[0] }}</p>
          </div>
          <!-- Profesi (Autocomplete) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Profesi / Bidang Pekerjaan</label>
            <div class="relative">
              <input
                v-model="formData.profession_search"
                type="text"
                placeholder="Ketik nama profesi..."
                autocomplete="off"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                @input="onProfessionSearch"
                @focus="showProfSuggestions = true"
              />
              <ul
                v-if="showProfSuggestions && profSuggestions.length"
                class="absolute left-0 right-0 top-full z-10 mt-1 max-h-48 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg"
              >
                <li
                  v-for="prof in profSuggestions"
                  :key="prof.id"
                  class="cursor-pointer px-3 py-2 text-sm hover:bg-primary-50"
                  @mousedown.prevent="selectProfession(prof)"
                >
                  {{ prof.name }}
                </li>
              </ul>
            </div>
          </div>
          <!-- Job Title -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan / Posisi *</label>
            <input
              v-model="formData.job_title"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            />
            <p v-if="formErrors.job_title" class="mt-1 text-xs text-red-500">{{ formErrors.job_title[0] }}</p>
          </div>
          <!-- Tanggal -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Mulai Bekerja *</label>
              <input
                v-model="formData.start_date"
                type="date"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Selesai (kosong = sekarang)</label>
              <input
                v-model="formData.end_date"
                type="date"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
              />
            </div>
          </div>
          <!-- Tipe Pekerjaan -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Pekerjaan</label>
            <select
              v-model="formData.employment_type"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            >
              <option value="">— Pilih —</option>
              <option value="full_time">Full Time</option>
              <option value="part_time">Part Time</option>
              <option value="contract">Kontrak</option>
              <option value="freelance">Freelance</option>
              <option value="internship">Magang</option>
            </select>
          </div>
          <!-- Relevansi & Is Current -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Relevansi dengan Prodi</label>
              <select
                v-model="formData.job_relevance"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
              >
                <option value="">— Pilih —</option>
                <option value="very_relevant">Sangat Relevan</option>
                <option value="relevant">Relevan</option>
                <option value="somewhat_relevant">Cukup Relevan</option>
                <option value="not_relevant">Tidak Relevan</option>
              </select>
            </div>
            <div class="flex items-end pb-1">
              <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input v-model="formData.is_current" type="checkbox" class="rounded border-gray-300 text-primary-600" />
                Pekerjaan saat ini
              </label>
            </div>
          </div>
          <!-- Deskripsi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
            <textarea
              v-model="formData.description"
              rows="2"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
            />
          </div>
          <!-- Submit -->
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50" @click="closeForm">Batal</button>
            <button
              type="submit"
              :disabled="submitting"
              class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 disabled:opacity-60"
            >
              {{ submitting ? 'Menyimpan...' : (editTarget ? 'Simpan Perubahan' : 'Tambah Pekerjaan') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirm Delete -->
    <div v-if="showDeleteDialog" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="w-full max-w-sm rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-base font-semibold text-gray-900">Hapus Pekerjaan?</h3>
        <p class="mt-2 text-sm text-gray-600">
          Pekerjaan di <span class="font-medium">{{ deleteTarget?.institution?.name ?? deleteTarget?.company_name }}</span> akan dihapus sementara.
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
import { ref, reactive, onMounted } from 'vue'
import { useAlumniStore } from '@/stores/useAlumniStore'

const store = useAlumniStore()

// ── State ────────────────────────────────────────────────────────────────────
const showForm         = ref(false)
const editTarget       = ref(null)
const submitting       = ref(false)
const formErrors       = ref({})
const showDeleteDialog = ref(false)
const deleteTarget     = ref(null)
const deleteLoading    = ref(false)

// Autocomplete
const instSuggestions    = ref([])
const showInstSuggestions = ref(false)
const profSuggestions    = ref([])
const showProfSuggestions = ref(false)
let instTimer = null
let profTimer = null

const formData = reactive({
  institution_id:     null,
  institution_search: '',
  profession_id:      null,
  profession_search:  '',
  job_title:          '',
  start_date:         '',
  end_date:           '',
  employment_type:    '',
  job_relevance:      '',
  is_current:         false,
  description:        '',
})

// ── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(() => {
  store.fetchMyEmploymentHistories()
})

// ── Form ──────────────────────────────────────────────────────────────────────
function openCreateForm() {
  editTarget.value = null
  Object.assign(formData, {
    institution_id: null, institution_search: '',
    profession_id: null,  profession_search: '',
    job_title: '', start_date: '', end_date: '',
    employment_type: '', job_relevance: '',
    is_current: false, description: '',
  })
  formErrors.value = {}
  showForm.value   = true
}

function openEditForm(h) {
  editTarget.value = h
  Object.assign(formData, {
    institution_id:     h.institution_id  ?? null,
    institution_search: h.institution?.name ?? '',
    profession_id:      h.profession_id   ?? null,
    profession_search:  h.profession?.name ?? '',
    job_title:          h.job_title        ?? '',
    start_date:         h.start_date?.slice(0, 10) ?? '',
    end_date:           h.end_date?.slice(0, 10)   ?? '',
    employment_type:    h.employment_type  ?? '',
    job_relevance:      h.job_relevance    ?? '',
    is_current:         h.is_current       ?? false,
    description:        h.description      ?? '',
  })
  formErrors.value = {}
  showForm.value   = true
}

function closeForm() {
  showForm.value = false
  editTarget.value = null
  instSuggestions.value = []
  profSuggestions.value = []
}

async function submitForm() {
  submitting.value = true
  formErrors.value = {}
  const payload = {
    institution_id:  formData.institution_id,
    profession_id:   formData.profession_id  || null,
    job_title:       formData.job_title,
    start_date:      formData.start_date,
    end_date:        formData.end_date       || null,
    employment_type: formData.employment_type || null,
    job_relevance:   formData.job_relevance   || null,
    is_current:      formData.is_current,
    description:     formData.description     || null,
  }
  try {
    if (editTarget.value) {
      await store.updateMyEmploymentHistory(editTarget.value.id, payload)
    } else {
      await store.createMyEmploymentHistory(payload)
    }
    closeForm()
    store.fetchMyEmploymentHistories()
  } catch (err) {
    if (err.response?.status === 422) formErrors.value = err.response.data.errors ?? {}
  } finally {
    submitting.value = false
  }
}

// ── Autocomplete ──────────────────────────────────────────────────────────────
function onInstitutionSearch() {
  clearTimeout(instTimer)
  formData.institution_id = null
  instTimer = setTimeout(async () => {
    instSuggestions.value = await store.searchInstitutionsPublic(formData.institution_search)
    showInstSuggestions.value = true
  }, 300)
}

function selectInstitution(inst) {
  formData.institution_id     = inst.id
  formData.institution_search = inst.name
  showInstSuggestions.value   = false
}

function onProfessionSearch() {
  clearTimeout(profTimer)
  formData.profession_id = null
  profTimer = setTimeout(async () => {
    profSuggestions.value = await store.searchProfessionsPublic(formData.profession_search)
    showProfSuggestions.value = true
  }, 300)
}

function selectProfession(prof) {
  formData.profession_id     = prof.id
  formData.profession_search = prof.name
  showProfSuggestions.value  = false
}

// ── Delete / Restore ──────────────────────────────────────────────────────────
function confirmDelete(h) {
  deleteTarget.value     = h
  showDeleteDialog.value = true
}

async function handleDelete() {
  deleteLoading.value = true
  try {
    await store.deleteMyEmploymentHistory(deleteTarget.value.id)
    showDeleteDialog.value = false
    deleteTarget.value     = null
    store.fetchMyEmploymentHistories()
  } finally {
    deleteLoading.value = false
  }
}

async function handleRestore(id) {
  await store.restoreMyEmploymentHistory(id)
  store.fetchMyEmploymentHistories()
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function formatDate(val) {
  if (!val) return ''
  return new Date(val).toLocaleDateString('id-ID', { month: 'short', year: 'numeric' })
}
</script>
