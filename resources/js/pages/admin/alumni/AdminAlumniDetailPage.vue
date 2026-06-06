<template>
  <div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500">
      <router-link to="/admin/dashboard" class="hover:text-gray-700">Dashboard</router-link>
      <span>/</span>
      <router-link to="/admin/alumni" class="hover:text-gray-700">Alumni</router-link>
      <span>/</span>
      <span class="text-gray-900 font-medium">{{ store.currentAlumni?.name ?? 'Detail' }}</span>
    </nav>

    <!-- Loading Skeleton -->
    <div v-if="store.loading && !store.currentAlumni" class="space-y-4">
      <div class="h-48 animate-pulse rounded-xl bg-gray-100" />
      <div class="h-64 animate-pulse rounded-xl bg-gray-100" />
    </div>

    <template v-else-if="store.currentAlumni">
      <!-- Profile Card -->
      <div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        <!-- Banner -->
        <div class="h-20 bg-gradient-to-r from-primary-600 to-primary-400" />
        <div class="px-6 pb-6">
          <div class="flex items-end gap-4 -mt-10">
            <!-- Avatar -->
            <div class="relative shrink-0">
              <img
                v-if="store.currentAlumni.photo"
                :src="store.currentAlumni.photo"
                :alt="store.currentAlumni.name"
                class="h-20 w-20 rounded-full border-4 border-white object-cover shadow"
              />
              <div
                v-else
                class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-white bg-primary-100 shadow text-2xl font-bold text-primary-700"
              >
                {{ initials(store.currentAlumni.name) }}
              </div>
              <span
                v-if="store.currentAlumni.deleted_at"
                class="absolute -top-1 -right-1 rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white"
              >Dihapus</span>
            </div>
            <!-- Name + NIM -->
            <div class="mb-1 flex-1">
              <h1 class="text-xl font-bold text-gray-900">{{ store.currentAlumni.name }}</h1>
              <p class="text-sm text-gray-500">NIM: {{ store.currentAlumni.nim }}</p>
            </div>
            <!-- Action Buttons -->
            <div class="flex items-center gap-2 mb-1">
              <button
                v-if="!store.currentAlumni.deleted_at"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50"
                @click="openEditModal"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.1 2.1 0 1 1 2.97 2.97L8.88 18.41l-4 1 1-4 11.982-10.923Z" />
                </svg>
                Edit
              </button>
              <button
                v-if="!store.currentAlumni.deleted_at"
                class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-1.5 text-sm text-red-600 hover:bg-red-50"
                @click="confirmDelete"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16" />
                </svg>
                Hapus
              </button>
              <button
                v-else
                class="inline-flex items-center gap-1.5 rounded-lg border border-green-200 px-3 py-1.5 text-sm text-green-600 hover:bg-green-50"
                @click="handleRestore"
              >
                Pulihkan
              </button>
            </div>
          </div>

          <!-- Info Grid -->
          <div class="mt-4 grid grid-cols-2 gap-x-8 gap-y-3 text-sm sm:grid-cols-3">
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Program Studi</p>
              <p class="mt-0.5 font-medium text-gray-900">{{ store.currentAlumni.study_program?.name ?? '—' }}</p>
              <p class="text-xs text-gray-500">{{ store.currentAlumni.study_program?.faculty?.name ?? '' }}</p>
            </div>
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Tahun Lulus</p>
              <p class="mt-0.5 font-medium text-gray-900">{{ store.currentAlumni.graduation_year ?? '—' }}</p>
            </div>
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">IPK</p>
              <p class="mt-0.5 font-medium text-gray-900">{{ store.currentAlumni.gpa ?? '—' }}</p>
            </div>
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Email</p>
              <p class="mt-0.5 font-medium text-gray-900 truncate">{{ store.currentAlumni.user?.email ?? '—' }}</p>
            </div>
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">No. HP</p>
              <p class="mt-0.5 font-medium text-gray-900">{{ store.currentAlumni.phone ?? '—' }}</p>
            </div>
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Jenis Kelamin</p>
              <p class="mt-0.5 font-medium text-gray-900">{{ genderLabel(store.currentAlumni.gender) }}</p>
            </div>
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Status Kerja</p>
              <span :class="employmentBadgeClass(store.currentAlumni.employment_status)" class="mt-0.5 inline-flex rounded-full px-2 py-0.5 text-xs font-medium">
                {{ employmentLabel(store.currentAlumni.employment_status) }}
              </span>
            </div>
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Masa Tunggu</p>
              <p class="mt-0.5 font-medium text-gray-900">
                {{ store.currentAlumni.waiting_period_months != null ? store.currentAlumni.waiting_period_months + ' bulan' : '—' }}
              </p>
            </div>
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Kota</p>
              <p class="mt-0.5 font-medium text-gray-900">{{ store.currentAlumni.city ?? '—' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab Navigation -->
      <div class="rounded-xl border border-gray-200 bg-white">
        <div class="flex border-b border-gray-200">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            :class="[
              'px-5 py-3 text-sm font-medium border-b-2 -mb-px transition-colors',
              activeTab === tab.key
                ? 'border-primary-600 text-primary-600'
                : 'border-transparent text-gray-500 hover:text-gray-700',
            ]"
            @click="activeTab = tab.key"
          >
            {{ tab.label }}
          </button>
        </div>

        <div class="p-5">
          <!-- Tab: Riwayat Pekerjaan -->
          <AlumniEmploymentTab
            v-if="activeTab === 'employment'"
            :alumni-id="alumniId"
          />

          <!-- Tab: Informasi Tambahan -->
          <div v-if="activeTab === 'info'" class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Alamat</p>
              <p class="mt-0.5 text-gray-800">{{ store.currentAlumni.address ?? '—' }}</p>
            </div>
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Kecamatan / Kota / Provinsi</p>
              <p class="mt-0.5 text-gray-800">
                {{ [store.currentAlumni.district, store.currentAlumni.city, store.currentAlumni.province].filter(Boolean).join(', ') || '—' }}
              </p>
            </div>
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Kode Pos</p>
              <p class="mt-0.5 text-gray-800">{{ store.currentAlumni.postal_code ?? '—' }}</p>
            </div>
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Tanggal Lahir</p>
              <p class="mt-0.5 text-gray-800">{{ formatDate(store.currentAlumni.birth_date) }}</p>
            </div>
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Tempat Lahir</p>
              <p class="mt-0.5 text-gray-800">{{ store.currentAlumni.birth_place ?? '—' }}</p>
            </div>
            <div>
              <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Dibuat</p>
              <p class="mt-0.5 text-gray-800">{{ formatDate(store.currentAlumni.created_at) }}</p>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Not Found -->
    <div v-else class="flex flex-col items-center justify-center py-16 text-center">
      <p class="text-sm text-gray-500">Data alumni tidak ditemukan.</p>
      <router-link to="/admin/alumni" class="mt-3 text-sm text-primary-600 hover:underline">Kembali ke Daftar Alumni</router-link>
    </div>

    <!-- Modal Edit -->
    <AlumniFormStepper
      v-if="showEditModal"
      :alumni="store.currentAlumni"
      @close="showEditModal = false"
      @saved="onSaved"
    />

    <!-- Confirm Delete -->
    <div v-if="showDeleteDialog" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="w-full max-w-sm rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-base font-semibold text-gray-900">Hapus Alumni?</h3>
        <p class="mt-2 text-sm text-gray-600">Alumni ini akan dihapus sementara dan dapat dipulihkan kembali.</p>
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
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAlumniStore } from '@/stores/useAlumniStore'
import AlumniEmploymentTab from '@/pages/admin/alumni/AlumniEmploymentTab.vue'
import AlumniFormStepper from '@/pages/admin/alumni/AlumniFormStepper.vue'

const route  = useRoute()
const router = useRouter()
const store  = useAlumniStore()

const alumniId = computed(() => route.params.id)

const tabs = [
  { key: 'employment', label: 'Riwayat Pekerjaan' },
  { key: 'info',       label: 'Informasi Tambahan' },
]
const activeTab = ref('employment')

const showEditModal    = ref(false)
const showDeleteDialog = ref(false)
const deleteLoading    = ref(false)

onMounted(() => {
  store.fetchAlumniById(alumniId.value)
})

function initials(name) {
  if (!name) return '?'
  return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase()
}

function formatDate(val) {
  if (!val) return '—'
  return new Date(val).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const GENDER_MAP = { male: 'Laki-laki', female: 'Perempuan', other: 'Lainnya' }
function genderLabel(g)  { return GENDER_MAP[g] ?? g ?? '—' }

const EMP_MAP   = { employed: 'Bekerja', self_employed: 'Wirausaha', unemployed: 'Belum Bekerja', continuing_study: 'Lanjut Studi' }
const EMP_CLASS = {
  employed:          'bg-green-100 text-green-700',
  self_employed:     'bg-blue-100 text-blue-700',
  unemployed:        'bg-orange-100 text-orange-700',
  continuing_study:  'bg-purple-100 text-purple-700',
}
function employmentLabel(s)      { return EMP_MAP[s]   ?? s ?? '—' }
function employmentBadgeClass(s) { return EMP_CLASS[s] ?? 'bg-gray-100 text-gray-600' }

function openEditModal() {
  showEditModal.value = true
}

async function onSaved() {
  showEditModal.value = false
  await store.fetchAlumniById(alumniId.value)
}

function confirmDelete() {
  showDeleteDialog.value = true
}

async function handleDelete() {
  deleteLoading.value = true
  try {
    await store.deleteAlumni(alumniId.value)
    showDeleteDialog.value = false
    router.push('/admin/alumni')
  } finally {
    deleteLoading.value = false
  }
}

async function handleRestore() {
  await store.restoreAlumni(alumniId.value)
  await store.fetchAlumniById(alumniId.value)
}
</script>
