<template>
  <Transition
    enter-active-class="transition duration-200 ease-out"
    enter-from-class="translate-y-1 opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transition duration-150 ease-in"
    leave-from-class="translate-y-0 opacity-100"
    leave-to-class="translate-y-1 opacity-0"
  >
    <div
      v-if="visible"
      role="alert"
      class="flex items-start gap-3 rounded-xl border px-4 py-3 text-sm"
      :class="variantClasses"
    >
      <!-- Icon -->
      <span class="mt-0.5 shrink-0" aria-hidden="true">
        <svg v-if="variant === 'success'" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M22 4L12 14.01l-3-3" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <svg v-else-if="variant === 'error'" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10" />
          <path d="M15 9l-6 6M9 9l6 6" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <svg v-else-if="variant === 'warning'" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
          <line x1="12" y1="9" x2="12" y2="13" stroke-linecap="round" />
          <line x1="12" y1="17" x2="12.01" y2="17" stroke-linecap="round" />
        </svg>
        <svg v-else class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10" />
          <line x1="12" y1="8" x2="12" y2="12" stroke-linecap="round" />
          <line x1="12" y1="16" x2="12.01" y2="16" stroke-linecap="round" />
        </svg>
      </span>

      <!-- Message -->
      <div class="flex-1 leading-relaxed">
        <p v-if="title" class="font-semibold">{{ title }}</p>
        <slot />
      </div>

      <!-- Dismiss button -->
      <button
        v-if="dismissible"
        type="button"
        class="ml-auto shrink-0 rounded-lg p-0.5 opacity-60 transition hover:opacity-100 focus:outline-none focus:ring-2"
        :class="dismissRingClass"
        aria-label="Tutup pesan"
        @click="dismiss"
      >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6L6 18M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  variant:     { type: String, default: 'info', validator: v => ['success','error','warning','info'].includes(v) },
  title:       { type: String, default: '' },
  dismissible: { type: Boolean, default: false },
  show:        { type: Boolean, default: true },
})

const emit = defineEmits(['close'])

const manuallyDismissed = ref(false)
const visible = computed(() => props.show && !manuallyDismissed.value)

function dismiss() {
  manuallyDismissed.value = true
  emit('close')
}

const variantClasses = computed(() => ({
  success: 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300',
  error:   'border-red-200   bg-red-50   text-red-800   dark:border-red-800   dark:bg-red-950/40   dark:text-red-300',
  warning: 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-300',
  info:    'border-sky-200   bg-sky-50   text-sky-800   dark:border-sky-800   dark:bg-sky-950/40   dark:text-sky-300',
}[props.variant]))

const dismissRingClass = computed(() => ({
  success: 'focus:ring-emerald-500',
  error:   'focus:ring-red-500',
  warning: 'focus:ring-amber-500',
  info:    'focus:ring-sky-500',
}[props.variant]))
</script>
