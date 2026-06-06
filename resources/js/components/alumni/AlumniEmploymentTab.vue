<template>
  <div class="space-y-4">
    <!-- Header: judul + tombol tambah -->
    <div class="flex items-center justify-between">
      <div>
        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Riwayat Pekerjaan</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400">{{ store.employmentMeta.total }} entri tercatat</p>
      </div>
      <AppButton
        v-if="canEdit"
        variant="primary"
        size="sm"
        @click="openCreate"
      >
        + Tambah Pekerjaan
      </AppButton>
    </div>

    <!-- Loading skeleton -->
    <div v-if="store.loading" class="space-y-3">
      <div v-for="i in 3" :key="i" class="flex gap-3">
        <AppSkeleton class="h-10 w-10 rounded-full" />
        <div class="flex-1 space-y-2">
          <AppSkeleton class="h-4 w-2/3" />
          <AppSkeleton class="h-3 w-1/3" />
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div
      v-else-if="!store.employmentHistories.length"
      class="flex flex-col items-center gap-3 rounded-2xl border-2 border-dashed border-slate-200 py-10 text-center dark:border-slate-700"
    >
      <svg class="h-10 w-10 text-slate-300 dark:text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <rect x="2" y="7" width="20" height="14" rx="2" />
        <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke-linecap="round" />
      </svg>
      <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Belum ada riwayat pekerjaan</p>
      <p class="text-xs text-slate-400 dark:text-slate-500">Klik "Tambah Pekerjaan" untuk menambahkan data.</p>
    </div>

    <!-- List riwayat pekerjaan -->
    <div v-else class="space-y-3">
      <div
        v-for="item in store.employmentHistories"
        :key="item.id"
        class="relative flex gap-4 rounded-2xl border bg-white p-4 shadow-sm transition
               dark:border-slate-700 dark:bg-slate-900"
        :class="item.deleted_at
          ? 'border-red-100 bg-red-50/30 dark:border-red-900 dark:bg-red-950/20'
          : 'border-slate-200'"
      >
        <!-- Icon status current -->
        <div
          class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
          :class="item.is_current
            ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900 dark:text-emerald-400'
            : 'bg-slate-100 text-slate-400 dark:bg-slate-800'"
        >
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="7" width="20" height="14" rx="2" />
            <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke-linecap="round" />
          </svg>
        </div>

        <!-- Detail pekerjaan -->
        <div class="min-w-0 flex-1">
          <div class="flex flex-wrap items-start gap-2">
            <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-200">
              {{ item.position }}
            </p>
            <AppBadge v-if="item.is_current" variant="success" size="sm">Aktif</AppBadge>
            <AppBadge v-if="item.deleted_at" variant="danger"  size="sm">Diarsipkan</AppBadge>
          </div>
          <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
            {{ item.institution?.name ?? '—' }} &middot; {{ item.profession?.name ?? '—' }}
          </p>
          <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">
            {{ formatPeriod(item) }}
            <span v-if="item.employment_type" class="ml-1.5 capitalize">&middot; {{ formatType(item.employment_type) }}</span>
          </p>
        </div>

        <!-- Action buttons -->
        <div v-if="canEdit" class="flex shrink-0 items-center gap-1">
          <template v-if="!item.deleted_at">
            <button
              type="button"
              title="Edit"
              class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-300"
              @click="openEdit(item)"
            >
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
            <button
              type="button"
              title="Hapus"
              class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-950 dark:hover:text-red-400"
              @click="confirmDelete(item)"
            >
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6" stroke-linecap="round" />
                <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" stroke-linecap="round" />
                <path d="M10 11v6M14 11v6" stroke-linecap="round" />
                <path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2" stroke-linecap="round" />
              </svg>
            </button>
          </template>
          <template v-else>
            <button
              type="button"
              title="Pulihkan"
              class="rounded-lg p-1.5 text-emerald-500 transition hover:bg-emerald-50 dark:hover:bg-emerald-950"
              @click="restoreHistory(item)"
            >
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 12a9 9 0 105.168-8.185M3 3v5h5" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </template>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <AppPagination
      v-if="store.employmentMeta.last_page > 1"
      :current-page="store.employmentMeta.current_page"
      :last-page="store.employmentMeta.last_page"
      @change="onPageChange"
    />

    <!-- Modal form -->
    <EmploymentFormModal
      :show="modalOpen"
      :alumni-id="alumniId"
      :history="editingHistory"
      context="admin"
      @close="modalOpen = false"
      @saved="onSaved"
    />

    <!-- Confirm delete -->
    <AppConfirm
      :show="confirmOpen"
      title="Hapus Riwayat Pekerjaan?"
      :message="`Data pekerjaan di '${deletingItem?.institution?.name ?? ''}' akan diarsipkan dan dapat dipulihkan.`"
      confirm-text="Hapus"
      variant="danger"
      :loading="deleting"
      @confirm="doDelete"
      @cancel="confirmOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppButton       from '@/components/base/AppButton.vue'
import AppBadge        from '@/components/base/AppBadge.vue'
import AppSkeleton     from '@/components/base/AppSkeleton.vue'
import AppPagination   from '@/components/base/AppPagination.vue'
import AppConfirm      from '@/components/base/AppConfirm.vue'
import EmploymentFormModal from '@/components/alumni/EmploymentFormModal.vue'
import { useAlumniStore } from '@/stores/useAlumniStore'

const props = defineProps({
  alumniId: { type: String, required: true },
  canEdit:  { type: Boolean, default: true },
})

const store = useAlumniStore()

// ── Modal state ────────────────────────────────────────────────────────────
const modalOpen      = ref(false)
const editingHistory = ref(null)

function openCreate() {
  editingHistory.value = null
  modalOpen.value = true
}
function openEdit(item) {
  editingHistory.value = item
  modalOpen.value = true
}
async function onSaved() {
  modalOpen.value = false
  await store.fetchEmploymentHistories(props.alumniId)
}

// ── Delete state ───────────────────────────────────────────────────────────
const confirmOpen  = ref(false)
const deletingItem = ref(null)
const deleting     = ref(false)

function confirmDelete(item) {
  deletingItem.value = item
  confirmOpen.value  = true
}
async function doDelete() {
  if (!deletingItem.value) return
  deleting.value = true
  try {
    await store.deleteEmploymentHistory(props.alumniId, deletingItem.value.id)
    confirmOpen.value = false
  } finally {
    deleting.value = false
  }
}
async function restoreHistory(item) {
  await store.restoreEmploymentHistory(props.alumniId, item.id)
  await store.fetchEmploymentHistories(props.alumniId)
}

// ── Pagination ─────────────────────────────────────────────────────────────
async function onPageChange(page) {
  await store.fetchEmploymentHistories(props.alumniId, { page })
}

// ── Formatters ─────────────────────────────────────────────────────────────
const monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']

function formatDate(dateStr) {
  if (!dateStr) return null
  const d = new Date(dateStr)
  return `${monthNames[d.getMonth()]} ${d.getFullYear()}`
}

function formatPeriod(item) {
  const start = formatDate(item.start_date)
  if (item.is_current) return `${start} – Sekarang`
  const end = formatDate(item.end_date)
  return end ? `${start} – ${end}` : (start ?? '—')
}

const typeLabels = {
  full_time:    'Full Time',
  part_time:    'Part Time',
  freelance:    'Freelance',
  internship:   'Magang',
  contract:     'Kontrak',
  entrepreneur: 'Wirausaha',
  other:        'Lainnya',
}
function formatType(type) {
  return typeLabels[type] ?? type
}

// ── Init ───────────────────────────────────────────────────────────────────
onMounted(() => {
  store.fetchEmploymentHistories(props.alumniId)
})
</script>
