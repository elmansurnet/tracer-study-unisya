<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Profil Saya</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Informasi data diri alumni</p>
      </div>
      <AppButton variant="secondary" size="sm" @click="openEdit">Edit Profil</AppButton>
    </div>

    <!-- Loading -->
    <div v-if="store.loading && !store.currentAlumni" class="space-y-4">
      <AppSkeleton class="mx-auto h-20 w-20 rounded-full" />
      <AppSkeleton class="mx-auto h-5 w-40" />
      <AppSkeleton class="h-4 w-full" />
      <AppSkeleton class="h-4 w-3/4" />
    </div>

    <!-- Profil card -->
    <div v-else-if="store.currentAlumni" class="grid gap-6 lg:grid-cols-3">
      <!-- Kolom kiri -->
      <div class="flex flex-col items-center gap-4 rounded-2xl border border-slate-200 bg-white p-6 text-center dark:border-slate-700 dark:bg-slate-900">
        <div
          class="flex h-20 w-20 items-center justify-center rounded-full text-2xl font-bold uppercase"
          :class="avatarClass"
        >
          <img v-if="a.photo_url" :src="a.photo_url" :alt="a.full_name" class="h-full w-full rounded-full object-cover" />
          <span v-else>{{ initials }}</span>
        </div>
        <div>
          <p class="text-base font-semibold text-slate-800 dark:text-slate-100">{{ a.full_name }}</p>
          <p class="mt-0.5 text-sm text-slate-500">{{ a.nim }}</p>
        </div>
        <AppBadge :variant="a.is_employed ? 'success' : 'warning'" size="sm">
          {{ a.is_employed ? 'Bekerja' : 'Belum Bekerja' }}
        </AppBadge>

        <!-- Toggle status kerja -->
        <div class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800">
          <p class="mb-2 text-xs font-medium text-slate-600 dark:text-slate-400">Update Status Pekerjaan</p>
          <div class="flex items-center justify-between gap-2">
            <span class="text-xs text-slate-500">{{ a.is_employed ? 'Sedang bekerja' : 'Belum bekerja' }}</span>
            <button
              type="button"
              role="switch"
              :aria-checked="a.is_employed"
              class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition focus:outline-none focus:ring-2 focus:ring-primary-500/50"
              :class="a.is_employed ? 'bg-primary-600' : 'bg-slate-300 dark:bg-slate-600'"
              :disabled="updatingStatus"
              @click="toggleEmploymentStatus"
            >
              <span
                class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow transition duration-200"
                :class="a.is_employed ? 'translate-x-4' : 'translate-x-0'"
              />
            </button>
          </div>
        </div>
      </div>

      <!-- Kolom kanan: data detail -->
      <div class="space-y-4 lg:col-span-2">
        <!-- Data Pribadi -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
          <h2 class="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-300">Data Pribadi</h2>
          <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div v-for="item in personalInfo" :key="item.label">
              <dt class="text-xs text-slate-500 dark:text-slate-400">{{ item.label }}</dt>
              <dd class="mt-0.5 text-sm font-medium text-slate-800 dark:text-slate-200">{{ item.value || '—' }}</dd>
            </div>
          </dl>
        </div>

        <!-- Data Akademik -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
          <h2 class="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-300">Data Akademik</h2>
          <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div v-for="item in academicInfo" :key="item.label">
              <dt class="text-xs text-slate-500 dark:text-slate-400">{{ item.label }}</dt>
              <dd class="mt-0.5 text-sm font-medium text-slate-800 dark:text-slate-200">{{ item.value || '—' }}</dd>
            </div>
          </dl>
        </div>
      </div>
    </div>

    <!-- Not found -->
    <AppAlert v-else variant="warning" :show="true">Data profil belum tersedia. Hubungi admin.</AppAlert>

    <!-- Modal edit -->
    <AppModal :show="modalOpen" title="Edit Profil" size="xl" @close="modalOpen = false">
      <AlumniFormStepper :alumni="store.currentAlumni" @saved="onSaved" @cancel="modalOpen = false" />
    </AppModal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAlumniStore } from '@/stores/useAlumniStore'
import AppButton         from '@/components/base/AppButton.vue'
import AppBadge          from '@/components/base/AppBadge.vue'
import AppSkeleton       from '@/components/base/AppSkeleton.vue'
import AppModal          from '@/components/base/AppModal.vue'
import AppAlert          from '@/components/base/AppAlert.vue'
import AlumniFormStepper from '@/components/alumni/AlumniFormStepper.vue'

const store = useAlumniStore()
const a     = computed(() => store.currentAlumni ?? {})

const avatarColors = [
  'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
  'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300',
  'bg-violet-100 text-violet-700 dark:bg-violet-900 dark:text-violet-300',
  'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
]
const initials    = computed(() => (a.value.full_name ?? '').split(' ').slice(0,2).map(w=>w[0]??'').join('').toUpperCase())
const avatarClass = computed(() => {
  if (a.value.photo_url) return 'bg-transparent'
  return avatarColors[(a.value.full_name?.charCodeAt(0) ?? 0) % avatarColors.length]
})

const personalInfo = computed(() => [
  { label: 'Jenis Kelamin', value: a.value.gender === 'L' ? 'Laki-laki' : a.value.gender === 'P' ? 'Perempuan' : null },
  { label: 'Tanggal Lahir', value: a.value.birth_date },
  { label: 'No. HP',        value: a.value.phone },
  { label: 'Email',         value: a.value.email },
  { label: 'Alamat',        value: a.value.address },
])

const academicInfo = computed(() => [
  { label: 'Program Studi', value: a.value.study_program?.name },
  { label: 'NIM',           value: a.value.nim },
  { label: 'Angkatan',      value: a.value.graduation_year },
  { label: 'Tanggal Lulus', value: a.value.graduation_date },
  { label: 'Skripsi',       value: a.value.thesis_title },
])

// Modal edit
const modalOpen = ref(false)
function openEdit() { modalOpen.value = true }
async function onSaved() {
  modalOpen.value = false
  await store.fetchMyProfile()
}

// Toggle status kerja
const updatingStatus = ref(false)
async function toggleEmploymentStatus() {
  if (updatingStatus.value) return
  updatingStatus.value = true
  try {
    await store.updateMyEmploymentStatus({ is_employed: !a.value.is_employed })
  } finally {
    updatingStatus.value = false
  }
}

onMounted(() => store.fetchMyProfile())
</script>
