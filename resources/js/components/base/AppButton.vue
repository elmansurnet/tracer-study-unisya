<template>
  <component
    :is="tag"
    :type="tag === 'button' ? type : undefined"
    :disabled="tag === 'button' ? (disabled || loading) : undefined"
    :aria-disabled="disabled || loading"
    :aria-busy="loading"
    :class="classes"
    v-bind="$attrs"
  >
    <!-- Loading spinner -->
    <svg
      v-if="loading"
      class="animate-spin shrink-0"
      :class="iconSizeClass"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      aria-hidden="true"
    >
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
    </svg>

    <!-- Left icon slot -->
    <slot v-else name="icon-left" />

    <!-- Label -->
    <span v-if="$slots.default" :class="{ 'sr-only': iconOnly }">
      <slot />
    </span>

    <!-- Right icon slot -->
    <slot name="icon-right" />
  </component>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  /** HTML tag atau Vue component yang dirender */
  tag: {
    type: [String, Object],
    default: 'button',
  },
  /** Tipe tombol (hanya berlaku jika tag === 'button') */
  type: {
    type: String,
    default: 'button',
    validator: (v) => ['button', 'submit', 'reset'].includes(v),
  },
  /** Varian tampilan tombol */
  variant: {
    type: String,
    default: 'primary',
    validator: (v) => ['primary', 'secondary', 'ghost', 'danger', 'warning', 'success', 'link'].includes(v),
  },
  /** Ukuran tombol */
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(v),
  },
  /** Apakah tombol disabled */
  disabled: {
    type: Boolean,
    default: false,
  },
  /** Apakah tombol sedang loading */
  loading: {
    type: Boolean,
    default: false,
  },
  /** Lebar penuh */
  block: {
    type: Boolean,
    default: false,
  },
  /** Tombol hanya ikon (label disembunyikan secara visual) */
  iconOnly: {
    type: Boolean,
    default: false,
  },
  /** Tombol rounded pill */
  pill: {
    type: Boolean,
    default: false,
  },
})

const variantClasses = {
  primary:   'bg-primary-600 text-white hover:bg-primary-700 active:bg-primary-800 focus-visible:outline-primary-600',
  secondary: 'bg-white text-primary-600 border border-primary-300 hover:bg-primary-50 active:bg-primary-100 focus-visible:outline-primary-500',
  ghost:     'bg-transparent text-gray-600 hover:bg-gray-100 active:bg-gray-200 focus-visible:outline-gray-400',
  danger:    'bg-red-600 text-white hover:bg-red-700 active:bg-red-800 focus-visible:outline-red-500',
  warning:   'bg-yellow-500 text-white hover:bg-yellow-600 active:bg-yellow-700 focus-visible:outline-yellow-500',
  success:   'bg-green-600 text-white hover:bg-green-700 active:bg-green-800 focus-visible:outline-green-500',
  link:      'bg-transparent text-primary-600 underline-offset-2 hover:underline active:text-primary-800 focus-visible:outline-primary-500 px-0 py-0',
}

const sizeClasses = {
  xs: 'px-2.5 py-1 text-xs gap-1',
  sm: 'px-3 py-1.5 text-xs gap-1.5',
  md: 'px-4 py-2 text-sm gap-2',
  lg: 'px-5 py-2.5 text-base gap-2',
  xl: 'px-6 py-3 text-base gap-2.5',
}

const iconSizeClasses = {
  xs: 'size-3',
  sm: 'size-3.5',
  md: 'size-4',
  lg: 'size-5',
  xl: 'size-5',
}

const iconSizeClass = computed(() => iconSizeClasses[props.size])

const classes = computed(() => [
  // Base
  'inline-flex items-center justify-center font-medium',
  'transition-all duration-150',
  'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2',
  'select-none',
  // Disabled / loading
  (props.disabled || props.loading) && 'opacity-50 cursor-not-allowed pointer-events-none',
  // Block
  props.block ? 'w-full' : '',
  // Icon only — make it square
  props.iconOnly ? 'aspect-square' : '',
  // Radius
  props.pill ? 'rounded-full' : 'rounded-lg',
  // Variant
  variantClasses[props.variant],
  // Size
  props.variant !== 'link' ? sizeClasses[props.size] : '',
])
</script>
