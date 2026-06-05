<template>
  <nav aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
      <li
        v-for="(item, index) in items"
        :key="`${item.label}-${index}`"
        class="inline-flex items-center gap-2"
      >
        <RouterLink
          v-if="item.to && index !== items.length - 1"
          :to="item.to"
          class="transition hover:text-primary-600 dark:hover:text-primary-300"
        >
          {{ item.label }}
        </RouterLink>

        <span
          v-else
          :class="index === items.length - 1 ? 'font-medium text-slate-700 dark:text-slate-200' : ''"
        >
          {{ item.label }}
        </span>

        <span v-if="index !== items.length - 1" aria-hidden="true">/</span>
      </li>
    </ol>
  </nav>
</template>

<script setup>
import { RouterLink } from 'vue-router'

defineProps({
  items: {
    type: Array,
    default: () => [],
  },
})
</script>