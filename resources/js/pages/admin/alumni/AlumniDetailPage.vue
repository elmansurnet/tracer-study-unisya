<template>
  <div class="space-y-6">
    <!-- Back + header -->
    <div class="flex items-center gap-3">
      <button
        type="button"
        class="rounded-xl border border-slate-200 bg-white p-2 text-slate-500 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-slate-800"
        @click="$router.back()"
      >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>
      <div class="flex-1">
        <h1 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Detail Alumni</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Informasi lengkap data alumni</p>
      </div>
      <AppButton variant="secondary" size="sm" @click="openEdit">Edit Data</AppButton>
    </div>

    <!-- Loading skeleton -->
    <div v-if="store.loading && !store.currentAlumni" class="grid gap-6 lg:grid-cols-3">
      <div class="space-y-4 lg:col-span-1">
        <AppSkeleton class="mx-auto h-24 w-24 rounded-full" />
        <AppSkeleton class="mx-auto h-5 w-40" />
        <AppSkeleton class="mx-auto h-4 w-28" />
      </div>
      <div class="space-y-3 lg:col-span-2">
        <AppSkeleton class="h-5 w-1/3" />
        <AppSkeleton class="h-4 w-full" />
        <AppSkeleton class="h-4 w-3/4" />
        <AppSkeleton class="h-4 w-2/3" />
      </div>
    </div>

    <!-- Content -->
    <div v-else-if="store.currentAlumni" class="grid gap-6 lg:grid-cols-3">
      <!-- Kolom kiri: avatar + identitas ringkas -->
      <div class="flex flex-col items-center gap-4 rounded-2xl border border-slate-200 bg-white p-6 text-center dark:border-slate-700 dark:bg-slate-900 lg:col-span-1">
        <!-- Avatar -->
        <div
          class="flex h-20 w-20 items-center justify-center rounded-full text-2xl font-bold uppercase"
          :class="avatarClass"
        >
          <img
            v-if="a.photo_url"
            :src="a.photo_url" :alt="a.full_name"
            class="h-full w-full rounded-full object-cover"
          />
          <span v-else>{{ initials }}</span>
        </div>

        <div>
          <p class="text-base font-semibold text-slate-800 dark:text-slate-100">{{ a.full_name }}</p>
          <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ a.nim }}</p>
        </div>

        <AppBadge :variant="a.is_employed ? 'success' : 'warning'" size="sm">
          {{ a.is_employed ? 'Bekerja' : 'Belum Bekerja' }}
        </AppBadge>

        <!-- Info ringkas -->
        <dl class="w-full divide-y divide-slate-100 text-left dark:divide-slate-800">
          <div v-for="item in sideInfo" :key="item.label" class="flex items-start justify-between gap-2 py-2.5">
            <dt class="shrink-0 text-xs text-slate-500 dark:text-slate-400">{{ item.label }}</dt>
            <dd class="text-right text-xs font-medium text-slate-700 dark:text-slate-300">{{ item.value || '—' }}</dd>
          </div>
        </dl>
      </div>

      <!-- Kolom kanan: detail + tab -->
      <div class="space-y-6 lg:col-span-2">
        <!-- Informasi Akademik -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
          <h2 class="mb-4 text-sm font-semibold text-slate-700 dark:text-slate-300">Informasi Akademik</h2>
          <dl class="grid grid-cols-2 gap-x-6 gap-y-3">
            <div v-for="item in academicInfo" :key="item.label">
              <dt class="text-xs text-slate-500 dark:text-slate-400">{{ item.label }}</dt>
              <dd class="mt-0.5 text-sm font-medium text-slate-800 dark:text-slate-200">{{ item.value || '—' }}</dd>
            </div>
          </dl>
        </div>

        <!-- Tab: Riwayat Pekerjaan -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
          <AlumniEmploymentTab :alumni-id="a.id" :can-edit="true" />
        </div>
      </div>
    </div>

    <!-- Not found -->
    <div v-else class="rounded-2xl border border-slate-200 py-16 text-center dark:border-slate-700">
      <p class="text-slate-500">Data alumni tidak ditemukan.</p>
      <AppButton class="mt-4" variant="ghost" size="sm" @click="$router.back()">Kembali</AppButton>
    </div>

    <!-- Modal edit -->
    <AppModal :show="modalOpen" title="Edit Data Alumni" size="xl" @close="modalOpen = false">
      <AlumniFormStepper :alumni="store.currentAlumni" @saved="onSaved" @cancel="modalOpen = false" />
    </AppModal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAlumniStore } from '@/stores/useAlumniStore'
import AppButton          from '@/components/base/AppButton.vue'
import AppBadge           from '@/components/base/AppBadge.vue'
import AppSkeleton        from '@/components/base/AppSkeleton.vue'
import AppModal           from '@/components/base/AppModal.vue'
import AlumniFormStepper  from '@/components/alumni/AlumniFormStepper.vue'
import AlumniEmploymentTab from '@/components/alumni/AlumniEmploymentTab.vue'

const route = useRoute()
const store = useAlumniStore()
const a     = computed(() => store.currentAlumni ?? {})

const avatarColors = [
  'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
  'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300',
  'bg-violet-100 text-violet-700 dark:bg-violet-900 dark:text-violet-300',
  'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
  'bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-300',
]
const initials   = computed(() => (a.value.full_name ?? '').split(' ').slice(0,2).map(w=>w[0]??'').join('').toUpperCase())
const avatarClass = computed(() => {
  if (a.value.photo_url) return 'bg-transparent'
  return avatarColors[(a.value.full_name?.charCodeAt(0) ?? 0) % avatarColors.length]
})

const sideInfo = computed(() => [
  { label: 'Jenis Kelamin', value: a.value.gender === 'L' ? 'Laki-laki' : a.value.gender === 'P' ? 'Perempuan' : null },
  { label: 'Tgl Lahir',     value: a.value.birth_date },
  { label: 'No. HP',        value: a.value.phone },
  { label: 'Email',         value: a.value.email },
])

const academicInfo = computed(() => [
  { label: 'Program Studi', value: a.value.study_program?.name },
  { label: 'Angkatan',      value: a.value.graduation_year },
  { label: 'Tgl Masuk',     value: a.value.entry_date },
  { label: 'Tgl Lulus',     value: a.value.graduation_date },
  { label: 'Skripsi',       value: a.value.thesis_title },
  { label: 'Masa Tunggu',   value: a.value.waiting_period_months != null ? `${a.value.waiting_period_months} bulan` : null },
])

const modalOpen = ref(false)
function openEdit() { modalOpen.value = true }
async function onSaved() {
  modalOpen.value = false
  await store.fetchAlumniById(route.params.id)
}

onMounted(() => store.fetchAlumniById(route.params.id))
</script>
