<template>
  <div
    :class="[
      'animate-pulse rounded-xl bg-slate-200 dark:bg-slate-800',
      className,
    ]"
  />
</template>

<script setup>
defineProps({
  className: {
    type: String,
    default: 'h-4 w-full',
  },
})
</script>