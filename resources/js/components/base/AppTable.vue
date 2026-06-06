<script setup>
import { computed } from 'vue'
import AppSkeleton from './AppSkeleton.vue'

const props = defineProps({
  columns:  { type: Array,   required: true },
  rows:     { type: Array,   default: () => [] },
  loading:  { type: Boolean, default: false },
  skeletonRows: { type: Number, default: 5 },
  emptyText: { type: String, default: 'Belum ada data.' },
  striped:  { type: Boolean, default: false },
})

const skeletonArray = computed(() => Array.from({ length: props.skeletonRows }))
</script>

<template>
  <div class="w-full overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
      <!-- Head -->
      <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
        <tr>
          <th
            v-for="col in columns"
            :key="col.key"
            :class="['px-4 py-3 font-semibold whitespace-nowrap', col.class ?? '']"
          >
            {{ col.label }}
          </th>
        </tr>
      </thead>

      <!-- Body: skeleton -->
      <tbody v-if="loading">
        <tr v-for="i in skeletonArray" :key="i" class="border-t border-gray-100 dark:border-gray-700">
          <td v-for="col in columns" :key="col.key" class="px-4 py-3">
            <AppSkeleton class="h-4 w-full rounded" />
          </td>
        </tr>
      </tbody>

      <!-- Body: empty -->
      <tbody v-else-if="rows.length === 0">
        <tr>
          <td :colspan="columns.length" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
            <div class="flex flex-col items-center gap-2">
              <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4" />
              </svg>
              <span class="text-sm">{{ emptyText }}</span>
            </div>
          </td>
        </tr>
      </tbody>

      <!-- Body: data -->
      <tbody v-else>
        <tr
          v-for="(row, idx) in rows"
          :key="row.id ?? idx"
          :class="[
            'border-t border-gray-100 dark:border-gray-700 transition-colors',
            striped && idx % 2 === 1 ? 'bg-gray-50/50 dark:bg-gray-800/30' : '',
            'hover:bg-primary-50/40 dark:hover:bg-primary-900/10'
          ]"
        >
          <td
            v-for="col in columns"
            :key="col.key"
            :class="['px-4 py-3 align-middle', col.cellClass ?? '']"
          >
            <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
              {{ row[col.key] ?? '—' }}
            </slot>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>