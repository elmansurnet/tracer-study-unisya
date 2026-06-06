<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Permohonan Saya</h1>
      <p class="mt-1 text-sm text-gray-500">Status permohonan aktivasi akun alumni Anda</p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-3">
      <div v-for="i in 2" :key="i" class="h-32 animate-pulse rounded-xl bg-gray-100" />
    </div>

    <!-- Empty -->
    <div
      v-else-if="!requests.length"
      class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-white py-14"
    >
      <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
      </svg>
      <p class="mt-3 text-sm font-medium text-gray-900">Belum ada permohonan</p>
      <p class="mt-1 text-xs text-gray-500">Anda belum pernah mengajukan permohonan.</p>
    </div>

    <!-- List Permohonan -->
    <div v-else class="space-y-4">
      <div
        v-for="req in requests"
        :key="req.id"
        class="rounded-xl border bg-white p-5"
        :class="borderClass(req.status)"
      >
        <!-- Header Kartu -->
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-sm font-semibold text-gray-900">Permohonan Aktivasi Akun</p>
            <p class="mt-0.5 text-xs text-gray-500">Diajukan {{ formatDate(req.created_at) }}</p>
          </div>
          <span :class="statusBadgeClass(req.status)" class="shrink-0 inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium">
            {{ statusLabel(req.status) }}
          </span>
        </div>

        <!-- Timeline Status -->
        <ol class="mt-4 space-y-3">
          <!-- Step 1: Pengajuan -->
          <li class="flex items-start gap-3">
            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100">
              <svg class="h-3.5 w-3.5 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
              </svg>
            </span>
            <div>
              <p class="text-sm font-medium text-gray-900">Permohonan Diajukan</p>
              <p class="text-xs text-gray-500">{{ formatDate(req.created_at) }}</p>
            </div>
          </li>

          <!-- Step 2: Review -->
          <li class="flex items-start gap-3">
            <span
              :class="req.status !== 'pending' ? 'bg-green-100' : 'bg-yellow-100'"
              class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full"
            >
              <svg
                v-if="req.status !== 'pending'"
                class="h-3.5 w-3.5 text-green-600"
                fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
              </svg>
              <svg
                v-else
                class="h-3.5 w-3.5 text-yellow-600"
                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
              </svg>
            </span>
            <div>
              <p class="text-sm font-medium text-gray-900">Sedang Ditinjau Admin</p>
              <p class="text-xs text-gray-500">
                {{ req.status === 'pending' ? 'Menunggu konfirmasi dari admin...' : formatDate(req.updated_at) }}
              </p>
            </div>
          </li>

          <!-- Step 3: Keputusan -->
          <li class="flex items-start gap-3">
            <span
              :class="decisionIconBg(req.status)"
              class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full"
            >
              <!-- Approved -->
              <svg
                v-if="req.status === 'approved'"
                class="h-3.5 w-3.5 text-green-600"
                fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
              </svg>
              <!-- Rejected -->
              <svg
                v-else-if="req.status === 'rejected'"
                class="h-3.5 w-3.5 text-red-600"
                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
              </svg>
              <!-- Pending -->
              <span v-else class="h-2 w-2 rounded-full bg-gray-400" />
            </span>
            <div>
              <p class="text-sm font-medium"
                :class="req.status === 'approved' ? 'text-green-700' : req.status === 'rejected' ? 'text-red-700' : 'text-gray-400'"
              >
                {{ req.status === 'approved' ? 'Permohonan Disetujui' : req.status === 'rejected' ? 'Permohonan Ditolak' : 'Menunggu Keputusan' }}
              </p>
              <p v-if="req.status === 'approved'" class="text-xs text-gray-500">Akun Anda telah aktif. Selamat datang!</p>
              <p v-else-if="req.status === 'rejected'" class="text-xs text-red-500">
                {{ req.note ? 'Catatan: ' + req.note : 'Hubungi admin untuk informasi lebih lanjut.' }}
              </p>
            </div>
          </li>
        </ol>
      </div>
    </div>

    <!-- Info Box -->
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
      <div class="flex items-start gap-3">
        <svg class="h-5 w-5 shrink-0 text-blue-600 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
        </svg>
        <div class="text-sm text-blue-800">
          <p class="font-medium">Informasi Proses</p>
          <p class="mt-0.5 text-xs text-blue-700">Proses verifikasi biasanya memakan waktu 1–3 hari kerja. Jika ada pertanyaan, hubungi admin melalui email atau datang langsung ke kampus.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import http from '@/lib/http'

// ── State ─────────────────────────────────────────────────────────────────────
const loading  = ref(false)
const requests = ref([])

// ── Lifecycle ─────────────────────────────────────────────────────────────────
onMounted(() => loadRequests())

// ── Methods ───────────────────────────────────────────────────────────────────
async function loadRequests() {
  loading.value = true
  try {
    const { data } = await http.get('/alumni/requests')
    requests.value = data.data ?? []
  } catch {
    requests.value = []
  } finally {
    loading.value = false
  }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
const STATUS_LABEL = { pending: 'Menunggu', approved: 'Disetujui', rejected: 'Ditolak' }
const STATUS_CLASS = {
  pending:  'bg-yellow-100 text-yellow-700',
  approved: 'bg-green-100 text-green-700',
  rejected: 'bg-red-100 text-red-700',
}
const BORDER_CLASS = {
  pending:  'border-yellow-200',
  approved: 'border-green-200',
  rejected: 'border-red-200',
}
const DECISION_BG = {
  approved: 'bg-green-100',
  rejected: 'bg-red-100',
}

function statusLabel(s)      { return STATUS_LABEL[s] ?? s ?? '—' }
function statusBadgeClass(s) { return STATUS_CLASS[s] ?? 'bg-gray-100 text-gray-600' }
function borderClass(s)      { return BORDER_CLASS[s] ?? 'border-gray-200' }
function decisionIconBg(s)   { return DECISION_BG[s]  ?? 'bg-gray-100' }

function formatDate(val) {
  if (!val) return '—'
  return new Date(val).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}
</script>
