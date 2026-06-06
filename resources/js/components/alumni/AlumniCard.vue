<template>
  <div
    class="group relative flex flex-col gap-4 rounded-2xl border bg-white p-5 shadow-sm transition
           hover:shadow-md dark:border-slate-700 dark:bg-slate-900"
    :class="isDeleted ? 'border-red-200 bg-red-50/40 dark:border-red-800 dark:bg-red-950/20' : 'border-slate-200'"
  >
    <!-- Soft-delete ribbon -->
    <div
      v-if="isDeleted"
      class="absolute right-3 top-3"
    >
      <AppBadge variant="danger" size="sm">Diarsipkan</AppBadge>
    </div>

    <!-- Header: avatar + identitas utama -->
    <div class="flex items-start gap-4">
      <!-- Avatar / inisial -->
      <div
        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full text-base font-semibold uppercase"
        :class="avatarClass"
      >
        <img
          v-if="alumni.photo_url"
          :src="alumni.photo_url"
          :alt="alumni.full_name"
          class="h-full w-full rounded-full object-cover"
          loading="lazy"
        />
        <span v-else>{{ initials }}</span>
      </div>

      <!-- Nama + NIM + Prodi -->
      <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">{{ alumni.full_name }}</p>
        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ alumni.nim }}</p>
        <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400">
          {{ alumni.study_program?.name ?? '—' }}
        </p>
      </div>
    </div>

    <!-- Meta info: angkatan, lulus, status kerja -->
    <div class="flex flex-wrap items-center gap-2">
      <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs text-slate-600 dark:bg-slate-800 dark:text-slate-300">
        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Angkatan {{ alumni.graduation_year ?? '—' }}
      </span>

      <AppBadge :variant="alumni.is_employed ? 'success' : 'warning'" size="sm">
        {{ alumni.is_employed ? 'Bekerja' : 'Belum Bekerja' }}
      </AppBadge>
    </div>

    <!-- Slot actions (tombol edit/hapus/lihat injeksi dari parent) -->
    <div v-if="$slots.actions" class="flex items-center justify-end gap-2 border-t border-slate-100 pt-3 dark:border-slate-800">
      <slot name="actions" :alumni="alumni" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import AppBadge from '@/components/base/AppBadge.vue'

const props = defineProps({
  alumni: { type: Object, required: true },
})

const isDeleted = computed(() => !!props.alumni.deleted_at)

const initials = computed(() => {
  const name = props.alumni.full_name ?? ''
  return name
    .split(' ')
    .slice(0, 2)
    .map(w => w[0] ?? '')
    .join('')
    .toUpperCase()
})

const avatarColors = [
  'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
  'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300',
  'bg-violet-100 text-violet-700 dark:bg-violet-900 dark:text-violet-300',
  'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
  'bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-300',
  'bg-cyan-100 text-cyan-700 dark:bg-cyan-900 dark:text-cyan-300',
]

const avatarClass = computed(() => {
  if (props.alumni.photo_url) return 'bg-transparent'
  // Konsisten per alumni berdasarkan karakter pertama nama
  const idx = (props.alumni.full_name?.charCodeAt(0) ?? 0) % avatarColors.length
  return avatarColors[idx]
})
</script>
