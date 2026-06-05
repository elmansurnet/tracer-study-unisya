<template>
  <span :class="badgeClass">
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'secondary',
  },
})

const badgeClass = computed(() => {
  const base = 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold'

  const variants = {
    primary: 'bg-primary-100 text-primary-700 dark:bg-primary-500/20 dark:text-primary-300',
    secondary: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
    success: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
    warning: 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
    danger: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
  }

  return [base, variants[props.variant] ?? variants.secondary]
})
</script>