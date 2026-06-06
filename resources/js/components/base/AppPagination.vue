<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: { type: Number, required: true },
  lastPage:    { type: Number, required: true },
  total:       { type: Number, default: 0 },
  perPage:     { type: Number, default: 15 },
  from:        { type: Number, default: 0 },
  to:          { type: Number, default: 0 },
})

const emit = defineEmits(['change'])

const pages = computed(() => {
  const range = []
  const delta = 2
  const left  = props.currentPage - delta
  const right = props.currentPage + delta

  for (let i = 1; i <= props.lastPage; i++) {
    if (i === 1 || i === props.lastPage || (i >= left && i <= right)) {
      range.push(i)
    }
  }

  const withEllipsis = []
  let prev = null
  for (const p of range) {
    if (prev && p - prev > 1) withEllipsis.push('...')
    withEllipsis.push(p)
    prev = p
  }
  return withEllipsis
})

const goTo = (page) => {
  if (typeof page !== 'number') return
  if (page < 1 || page > props.lastPage) return
  if (page === props.currentPage) return
  emit('change', page)
}
</script>

<template>
  <div class="flex flex-col sm:flex-row items-center justify-between gap-3 py-3">
    <!-- Info -->
    <p class="text-xs text-gray-500 dark:text-gray-400">
      Menampilkan <span class="font-medium text-gray-700 dark:text-gray-200">{{ from }}–{{ to }}</span>
      dari <span class="font-medium text-gray-700 dark:text-gray-200">{{ total }}</span> data
    </p>

    <!-- Navigation -->
    <nav class="flex items-center gap-1" aria-label="Pagination">
      <!-- Prev -->
      <button
        :disabled="currentPage <= 1"
        class="px-2 py-1.5 rounded text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700
               disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
        @click="goTo(currentPage - 1)"
        aria-label="Halaman sebelumnya"
      >
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <!-- Page buttons -->
      <template v-for="(p, i) in pages" :key="i">
        <span v-if="p === '...'" class="px-2 py-1 text-gray-400 text-sm select-none">…</span>
        <button
          v-else
          :class="[
            'min-w-[32px] px-2 py-1.5 rounded text-sm font-medium transition-colors',
            p === currentPage
              ? 'bg-primary-600 text-white shadow-sm'
              : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
          ]"
          @click="goTo(p)"
          :aria-current="p === currentPage ? 'page' : undefined"
        >
          {{ p }}
        </button>
      </template>

      <!-- Next -->
      <button
        :disabled="currentPage >= lastPage"
        class="px-2 py-1.5 rounded text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700
               disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
        @click="goTo(currentPage + 1)"
        aria-label="Halaman berikutnya"
      >
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </nav>
  </div>
</template>