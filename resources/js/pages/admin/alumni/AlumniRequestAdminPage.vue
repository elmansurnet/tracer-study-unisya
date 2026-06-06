<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-xl font-semibold text-gray-900">Permohonan Alumni</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola permohonan aktivasi akun alumni</p>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="flex flex-wrap items-center gap-3">
      <!-- Search -->
      <div class="relative flex-1 min-w-[200px]">
        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z" />
        </svg>
        <input
          v-model="filters.search"
          type="text"
          placeholder="Cari nama / NIM / email..."
          class="w-full rounded-lg border border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
          @input="onSearch"
        />
      </div>
      <!-- Status Filter -->
      <select
        v-model="filters.status"
        class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
        @change="loadRequests"
      >
        <option value="">Semua Status</option>
        <option value="pending">Menunggu</option>
        <option value="approved">Disetujui</option>
        <option value="rejected">Ditolak</option>
      </select>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 5" :key="i" class="h-20 animate-pulse rounded-xl bg-gray-100" />
    </div>

    <!-- Empty -->
    <div
      v-else-if="!requests.length"
      class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-white py-14"
    >
      <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
      </svg>
      <p class="mt-3 text-sm font-medium text-gray-900">Tidak ada permohonan</p>
      <p class="mt-1 text-xs text-gray-500">Belum ada permohonan yang masuk saat ini.</p>
    </div>

    <!-- List -->
    <div v-else class="overflow-hidden rounded-xl border border-gray-200 bg-white">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-200 bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
            <th class="px-4 py-3 text-left">Alumni</th>
            <th class="px-4 py-3 text-left">Program Studi</th>
            <th class="px-4 py-3 text-left">Tahun Lulus</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Tanggal</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr
            v-for="req in requests"
            :key="req.id"
            class="transition hover:bg-gray-50"
          >
            <td class="px-4 py-3">
              <p class="font-medium text-gray-900">{{ req.alumni?.name ?? '—' }}</p>
              <p class="text-xs text-gray-500">{{ req.alumni?.nim ?? '' }} · {{ req.alumni?.user?.email ?? '' }}</p>
            </td>
            <td class="px-4 py-3 text-gray-700">
              {{ req.alumni?.study_program?.name ?? '—' }}
            </td>
            <td class="px-4 py-3 text-gray-700">
              {{ req.alumni?.graduation_year ?? '—' }}
            </td>
            <td class="px-4 py-3">
              <span :class="statusBadgeClass(req.status)" class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium">
                {{ statusLabel(req.status) }}
              </span>
            </td>
            <td class="px-4 py-3 text-gray-500">
              {{ formatDate(req.created_at) }}
            </td>
            <td class="px-4 py-3">
              <div class="flex justify-end gap-2">
                <button
                  v-if="req.status === 'pending'"
                  class="rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700"
                  @click="openConfirm(req, 'approved')"
                >
                  Setujui
                </button>
                <button
                  v-if="req.status === 'pending'"
                  class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50"
                  @click="openConfirm(req, 'rejected')"
                >
                  Tolak
                </button>
                <router-link
                  v-if="req.alumni?.id"
                  :to="`/admin/alumni/${req.alumni.id}`"
                  class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50"
                >
                  Lihat Alumni
                </router-link>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="flex items-center justify-between border-t border-gray-200 px-4 py-3">
        <p class="text-xs text-gray-500">
          Menampilkan {{ meta.from }}–{{ meta.to }} dari {{ meta.total }} permohonan
        </p>
        <div class="flex gap-1">
          <button
            :disabled="meta.current_page <= 1"
            class="rounded border border-gray-300 px-3 py-1.5 text-xs hover:bg-gray-50 disabled:opacity-40"
            @click="goToPage(meta.current_page - 1)"
          >
            ‹ Prev
          </button>
          <button
            :disabled="meta.current_page >= meta.last_page"
            class="rounded border border-gray-300 px-3 py-1.5 text-xs hover:bg-gray-50 disabled:opacity-40"
            @click="goToPage(meta.current_page + 1)"
          >
            Next ›
          </button>
        </div>
      </div>
    </div>

    <!-- Confirm Dialog -->
    <div v-if="showConfirm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="w-full max-w-sm rounded-xl bg-white p-6 shadow-xl">
        <h3 class="text-base font-semibold text-gray-900">
          {{ confirmAction === 'approved' ? 'Setujui Permohonan?' : 'Tolak Permohonan?' }}
        </h3>
        <p class="mt-2 text-sm text-gray-600">
          Permohonan dari <span class="font-medium">{{ confirmTarget?.alumni?.name }}</span> akan
          {{ confirmAction === 'approved' ? 'disetujui dan akun alumni akan diaktifkan.' : 'ditolak.' }}
        </p>
        <!-- Catatan Penolakan -->
        <div v-if="confirmAction === 'rejected'" class="mt-3">
          <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
          <textarea
            v-model="rejectNote"
            rows="2"
            placeholder="Alasan penolakan..."
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
          />
        </div>
        <div class="mt-4 flex justify-end gap-2">
          <button
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50"
            @click="showConfirm = false"
          >
            Batal
          </button>
          <button
            :class="confirmAction === 'approved' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'"
            class="rounded-lg px-4 py-2 text-sm font-medium text-white disabled:opacity-60"
            :disabled="actionLoading"
            @click="submitAction"
          >
            {{ actionLoading ? 'Memproses...' : (confirmAction === 'approved' ? 'Ya, Setujui' : 'Ya, Tolak') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import http from '@/lib/http'

// ── State ─────────────────────────────────────────────────────────────────────
const loading  = ref(false)
const requests = ref([])
const meta     = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0 })

const filters = reactive({
  search: '',
  status: '',
  page:   1,
})

const showConfirm    = ref(false)
const confirmTarget  = ref(null)
const confirmAction  = ref('')   // 'approved' | 'rejected'
const rejectNote     = ref('')
const actionLoading  = ref(false)

let searchTimer = null

// ── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(() => loadRequests())

// ── Methods ───────────────────────────────────────────────────────────────────
async function loadRequests() {
  loading.value = true
  try {
    const { data } = await http.get('/admin/alumni/requests', {
      params: {
        search:   filters.search || undefined,
        status:   filters.status || undefined,
        page:     filters.page,
        per_page: 15,
      },
    })
    requests.value = data.data
    meta.value     = data.meta ?? meta.value
  } catch {
    requests.value = []
  } finally {
    loading.value = false
  }
}

function onSearch() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    filters.page = 1
    loadRequests()
  }, 400)
}

function goToPage(page) {
  filters.page = page
  loadRequests()
}

function openConfirm(req, action) {
  confirmTarget.value = req
  confirmAction.value = action
  rejectNote.value    = ''
  showConfirm.value   = true
}

async function submitAction() {
  actionLoading.value = true
  try {
    const payload = { status: confirmAction.value }
    if (confirmAction.value === 'rejected' && rejectNote.value.trim()) {
      payload.note = rejectNote.value.trim()
    }
    await http.patch(`/admin/alumni/requests/${confirmTarget.value.id}`, payload)
    showConfirm.value = false
    await loadRequests()
  } catch {
    // error handled silently — production: tambah toast notification
  } finally {
    actionLoading.value = false
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
const STATUS_LABEL = { pending: 'Menunggu', approved: 'Disetujui', rejected: 'Ditolak' }
const STATUS_CLASS = {
  pending:  'bg-yellow-100 text-yellow-700',
  approved: 'bg-green-100 text-green-700',
  rejected: 'bg-red-100 text-red-700',
}
function statusLabel(s)      { return STATUS_LABEL[s] ?? s ?? '—' }
function statusBadgeClass(s) { return STATUS_CLASS[s] ?? 'bg-gray-100 text-gray-600' }

function formatDate(val) {
  if (!val) return '—'
  return new Date(val).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>
