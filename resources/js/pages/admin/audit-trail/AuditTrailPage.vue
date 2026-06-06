<script setup>
import { onMounted, ref } from 'vue'
import { useAuditTrailStore } from '@/stores/useAuditTrailStore'

const store = useAuditTrailStore()
const showDetail = ref(false)

onMounted(() => store.fetchList())

function applyFilters() {
  store.fetchList(1)
}

function resetAndFetch() {
  store.resetFilters()
  store.fetchList(1)
}

async function openDetail(id) {
  await store.fetchDetail(id)
  showDetail.value = true
}

function closeDetail() {
  showDetail.value = false
  store.clearSelected()
}

const ACTION_LABELS = {
  CREATE:  { label: 'Buat',   color: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' },
  UPDATE:  { label: 'Ubah',   color: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' },
  DELETE:  { label: 'Hapus',  color: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' },
  RESTORE: { label: 'Pulihkan', color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' },
}

function formatModel(type) {
  if (!type) return '-'
  return type.split('\\').pop()
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Audit Trail</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Riwayat perubahan data sistem secara lengkap.</p>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
      <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <input
          v-model="store.filters.search"
          type="text"
          placeholder="Cari deskripsi / IP..."
          class="input-base"
          @keyup.enter="applyFilters"
        />
        <select v-model="store.filters.action" class="input-base" @change="applyFilters">
          <option value="">Semua Aksi</option>
          <option value="CREATE">Buat</option>
          <option value="UPDATE">Ubah</option>
          <option value="DELETE">Hapus</option>
          <option value="RESTORE">Pulihkan</option>
        </select>
        <input
          v-model="store.filters.date_from"
          type="date"
          class="input-base"
          placeholder="Dari tanggal"
          @change="applyFilters"
        />
        <input
          v-model="store.filters.date_to"
          type="date"
          class="input-base"
          placeholder="Sampai tanggal"
          @change="applyFilters"
        />
      </div>
      <div class="mt-3 flex items-center gap-2">
        <button class="btn-primary" @click="applyFilters">Terapkan</button>
        <button class="btn-ghost" @click="resetAndFetch">Reset</button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
      <div v-if="store.isLoading" class="flex items-center justify-center py-16">
        <svg class="h-8 w-8 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
        </svg>
      </div>
      <div v-else-if="store.error" class="py-12 text-center text-sm text-red-500">{{ store.error }}</div>
      <div v-else-if="!store.list.length" class="py-16 text-center text-sm text-gray-400">
        Tidak ada data audit trail.
      </div>
      <table v-else class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="table-th">Waktu</th>
            <th class="table-th">User</th>
            <th class="table-th">Aksi</th>
            <th class="table-th">Model</th>
            <th class="table-th">Deskripsi</th>
            <th class="table-th">IP Address</th>
            <th class="table-th w-16"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
          <tr
            v-for="item in store.list"
            :key="item.id"
            class="hover:bg-gray-50 dark:hover:bg-gray-700/50"
          >
            <td class="table-td whitespace-nowrap text-xs text-gray-500">
              {{ new Date(item.created_at).toLocaleString('id-ID') }}
            </td>
            <td class="table-td">
              <div class="text-sm font-medium text-gray-900 dark:text-white">
                {{ item.user?.name ?? 'System' }}
              </div>
              <div class="text-xs text-gray-400">{{ item.user?.email ?? '' }}</div>
            </td>
            <td class="table-td">
              <span
                class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                :class="ACTION_LABELS[item.action]?.color ?? 'bg-gray-100 text-gray-600'"
              >
                {{ ACTION_LABELS[item.action]?.label ?? item.action }}
              </span>
            </td>
            <td class="table-td text-sm text-gray-600 dark:text-gray-300">
              {{ formatModel(item.auditable_type) }}
            </td>
            <td class="table-td max-w-xs truncate text-sm text-gray-600 dark:text-gray-300">
              {{ item.description }}
            </td>
            <td class="table-td text-xs text-gray-400">{{ item.ip_address }}</td>
            <td class="table-td">
              <button class="text-xs text-primary hover:underline" @click="openDetail(item.id)">
                Detail
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="store.pagination.last_page > 1" class="flex items-center justify-between border-t border-gray-200 px-4 py-3 dark:border-gray-700">
        <span class="text-sm text-gray-500">
          Halaman {{ store.pagination.current_page }} dari {{ store.pagination.last_page }}
          ({{ store.pagination.total }} data)
        </span>
        <div class="flex gap-1">
          <button
            :disabled="store.pagination.current_page <= 1"
            class="btn-ghost px-3 py-1 text-sm disabled:opacity-40"
            @click="store.fetchList(store.pagination.current_page - 1)"
          >
            &larr; Prev
          </button>
          <button
            :disabled="store.pagination.current_page >= store.pagination.last_page"
            class="btn-ghost px-3 py-1 text-sm disabled:opacity-40"
            @click="store.fetchList(store.pagination.current_page + 1)"
          >
            Next &rarr;
          </button>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <Teleport to="body">
      <div v-if="showDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-2xl rounded-2xl bg-white shadow-xl dark:bg-gray-800">
          <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Detail Audit Trail</h2>
            <button class="text-gray-400 hover:text-gray-600" @click="closeDetail">
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div v-if="store.isLoadingDetail" class="flex justify-center py-10">
            <svg class="h-6 w-6 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
            </svg>
          </div>
          <div v-else-if="store.selected" class="space-y-4 p-6">
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div><span class="text-gray-500">User:</span> <span class="font-medium">{{ store.selected.user?.name ?? 'System' }}</span></div>
              <div><span class="text-gray-500">Aksi:</span> <span class="font-medium">{{ store.selected.action }}</span></div>
              <div><span class="text-gray-500">Model:</span> <span class="font-medium">{{ formatModel(store.selected.auditable_type) }}</span></div>
              <div><span class="text-gray-500">ID:</span> <span class="font-medium">{{ store.selected.auditable_id }}</span></div>
              <div><span class="text-gray-500">IP:</span> <span class="font-medium">{{ store.selected.ip_address }}</span></div>
              <div><span class="text-gray-500">Waktu:</span> <span class="font-medium">{{ new Date(store.selected.created_at).toLocaleString('id-ID') }}</span></div>
            </div>
            <div>
              <p class="mb-1 text-sm text-gray-500">Deskripsi</p>
              <p class="text-sm">{{ store.selected.description }}</p>
            </div>
            <div v-if="store.selected.old_values" class="rounded-lg bg-red-50 p-3 dark:bg-red-900/20">
              <p class="mb-1 text-xs font-medium text-red-600 dark:text-red-400">Nilai Lama</p>
              <pre class="overflow-auto text-xs">{{ JSON.stringify(store.selected.old_values, null, 2) }}</pre>
            </div>
            <div v-if="store.selected.new_values" class="rounded-lg bg-green-50 p-3 dark:bg-green-900/20">
              <p class="mb-1 text-xs font-medium text-green-600 dark:text-green-400">Nilai Baru</p>
              <pre class="overflow-auto text-xs">{{ JSON.stringify(store.selected.new_values, null, 2) }}</pre>
            </div>
          </div>
          <div class="flex justify-end border-t border-gray-200 px-6 py-4 dark:border-gray-700">
            <button class="btn-ghost" @click="closeDetail">Tutup</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
