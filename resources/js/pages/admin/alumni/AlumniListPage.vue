<template>
  <div class="space-y-5">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Manajemen Alumni</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Kelola data alumni UNISYA</p>
      </div>
      <AppButton variant="primary" size="sm" @click="openCreate">
        <svg class="mr-1.5 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Tambah Alumni
      </AppButton>
    </div>

    <!-- Stat bar -->
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
      <div
        v-for="stat in stats" :key="stat.label"
        class="flex flex-col gap-1 rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900"
      >
        <span class="text-xs text-slate-500 dark:text-slate-400">{{ stat.label }}</span>
        <span class="text-xl font-bold text-slate-800 dark:text-slate-100">{{ stat.value ?? '—' }}</span>
      </div>
    </div>

    <!-- Filter bar -->
    <div class="flex flex-wrap items-center gap-2">
      <!-- Search -->
      <div class="flex min-w-[200px] flex-1 items-center gap-2">
        <input
          v-model="filters.search"
          type="text"
          placeholder="Cari nama, NIM, email..."
          class="flex-1 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900
                 placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20
                 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
          @keyup.enter="applyFilters"
        />
        <AppButton variant="secondary" size="sm" @click="applyFilters">Cari</AppButton>
      </div>

      <!-- Filter angkatan -->
      <select
        v-model="filters.graduation_year"
        class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20
               dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
        @change="applyFilters"
      >
        <option value="">Semua Angkatan</option>
        <option v-for="y in store.graduationYears" :key="y" :value="y">{{ y }}</option>
      </select>

      <!-- Filter status kerja -->
      <select
        v-model="filters.is_employed"
        class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20
               dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
        @change="applyFilters"
      >
        <option value="">Semua Status</option>
        <option value="1">Bekerja</option>
        <option value="0">Belum Bekerja</option>
      </select>

      <!-- Toggle arsip -->
      <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
        <input v-model="filters.with_trashed" type="checkbox" class="rounded" @change="applyFilters" />
        Tampilkan arsip
      </label>
    </div>

    <!-- Loading skeleton grid -->
    <div v-if="store.loading" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="i in 6" :key="i" class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
        <div class="flex gap-4">
          <AppSkeleton class="h-12 w-12 rounded-full" />
          <div class="flex-1 space-y-2">
            <AppSkeleton class="h-4 w-3/4" />
            <AppSkeleton class="h-3 w-1/2" />
            <AppSkeleton class="h-3 w-2/3" />
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div
      v-else-if="!store.alumni.length"
      class="flex flex-col items-center gap-3 rounded-2xl border-2 border-dashed border-slate-200 py-16 text-center dark:border-slate-700"
    >
      <svg class="h-12 w-12 text-slate-300 dark:text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke-linecap="round" />
        <circle cx="9" cy="7" r="4" />
        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke-linecap="round" />
      </svg>
      <p class="font-medium text-slate-500 dark:text-slate-400">Belum ada data alumni</p>
      <p class="text-sm text-slate-400 dark:text-slate-500">Gunakan tombol "Tambah Alumni" untuk menambahkan data.</p>
    </div>

    <!-- Grid kartu alumni -->
    <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <AlumniCard
        v-for="item in store.alumni"
        :key="item.id"
        :alumni="item"
      >
        <template #actions="{ alumni: a }">
          <router-link
            :to="{ name: 'admin.alumni.detail', params: { id: a.id } }"
            class="text-xs font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400"
          >
            Detail
          </router-link>
          <button
            class="text-xs font-medium text-slate-500 hover:text-slate-700 dark:hover:text-slate-300"
            @click="openEdit(a)"
          >
            Edit
          </button>
          <button
            v-if="!a.deleted_at"
            class="text-xs font-medium text-red-500 hover:text-red-700"
            @click="confirmDelete(a)"
          >
            Hapus
          </button>
          <button
            v-else
            class="text-xs font-medium text-emerald-500 hover:text-emerald-700"
            @click="doRestore(a)"
          >
            Pulihkan
          </button>
        </template>
      </AlumniCard>
    </div>

    <!-- Pagination -->
    <AppPagination
      v-if="store.meta.last_page > 1"
      :current-page="store.meta.current_page"
      :last-page="store.meta.last_page"
      :total="store.meta.total"
      :from="store.meta.from"
      :to="store.meta.to"
      @change="onPageChange"
    />

    <!-- Modal form stepper -->
    <AppModal
      :show="modalOpen"
      :title="editTarget ? 'Edit Data Alumni' : 'Tambah Alumni Baru'"
      size="xl"
      @close="modalOpen = false"
    >
      <AlumniFormStepper
        :alumni="editTarget"
        @saved="onSaved"
        @cancel="modalOpen = false"
      />
    </AppModal>

    <!-- Confirm hapus -->
    <AppConfirm
      :show="confirmOpen"
      title="Hapus Alumni?"
      :message="`Data alumni '${deleteTarget?.full_name}' akan diarsipkan.`"
      confirm-text="Hapus"
      variant="danger"
      :loading="deleting"
      @confirm="doDelete"
      @cancel="confirmOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAlumniStore } from '@/stores/useAlumniStore'
import AppButton      from '@/components/base/AppButton.vue'
import AppPagination  from '@/components/base/AppPagination.vue'
import AppSkeleton    from '@/components/base/AppSkeleton.vue'
import AppModal       from '@/components/base/AppModal.vue'
import AppConfirm     from '@/components/base/AppConfirm.vue'
import AlumniCard         from '@/components/alumni/AlumniCard.vue'
import AlumniFormStepper  from '@/components/alumni/AlumniFormStepper.vue'

const store = useAlumniStore()

// ── Filters ─────────────────────────────────────────────────────────────
const filters = ref({
  search:          '',
  graduation_year: '',
  is_employed:     '',
  with_trashed:    false,
  page:            1,
  per_page:        12,
})

function applyFilters() {
  filters.value.page = 1
  store.fetchAlumni(buildParams())
}

function buildParams() {
  const p = { ...filters.value }
  if (!p.search)          delete p.search
  if (!p.graduation_year) delete p.graduation_year
  if (p.is_employed === '') delete p.is_employed
  if (!p.with_trashed)    delete p.with_trashed
  return p
}

function onPageChange(page) {
  filters.value.page = page
  store.fetchAlumni(buildParams())
}

// ── Stats bar ─────────────────────────────────────────────────────────────
const stats = computed(() => [
  { label: 'Total Alumni',    value: store.meta.total },
  { label: 'Bekerja',         value: store.employmentStats?.employed },
  { label: 'Belum Bekerja',   value: store.employmentStats?.not_employed },
  { label: 'Rata-rata Tunggu',value: store.employmentStats?.avg_waiting_months != null
      ? `${Math.round(store.employmentStats.avg_waiting_months)} bln` : null },
])

// ── Modal create/edit ────────────────────────────────────────────────────────
const modalOpen  = ref(false)
const editTarget = ref(null)

function openCreate() { editTarget.value = null; modalOpen.value = true }
function openEdit(a)  { editTarget.value = a;    modalOpen.value = true }
function onSaved()    { modalOpen.value = false; store.fetchAlumni(buildParams()) }

// ── Delete / restore ─────────────────────────────────────────────────────────
const confirmOpen  = ref(false)
const deleteTarget = ref(null)
const deleting     = ref(false)

function confirmDelete(a) { deleteTarget.value = a; confirmOpen.value = true }
async function doDelete() {
  deleting.value = true
  try {
    await store.deleteAlumni(deleteTarget.value.id)
    confirmOpen.value = false
    store.fetchAlumni(buildParams())
  } finally { deleting.value = false }
}
async function doRestore(a) {
  await store.restoreAlumni(a.id)
  store.fetchAlumni(buildParams())
}

// ── Init ───────────────────────────────────────────────────────────────────
onMounted(() => {
  store.fetchAlumni(buildParams())
  store.fetchGraduationYears()
  store.fetchEmploymentStats()
})
</script>
