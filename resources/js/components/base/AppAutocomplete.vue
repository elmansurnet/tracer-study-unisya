<template>
  <div ref="wrapperRef" class="relative" :class="wrapperClass">
    <!-- Label -->
    <label
      v-if="label"
      :for="inputId"
      class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300"
    >
      {{ label }}
      <span v-if="required" class="ml-0.5 text-red-500">*</span>
    </label>

    <!-- Input wrapper -->
    <div class="relative">
      <input
        :id="inputId"
        ref="inputRef"
        v-model="query"
        type="text"
        role="combobox"
        :aria-expanded="isOpen"
        :aria-activedescendant="activeOptionId"
        aria-autocomplete="list"
        :aria-controls="listId"
        :placeholder="placeholder"
        :disabled="disabled"
        autocomplete="off"
        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 pr-9 text-sm text-slate-900 shadow-sm transition
               placeholder:text-slate-400
               focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20
               disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400
               dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500
               dark:focus:border-primary-400 dark:focus:ring-primary-400/20"
        :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400/20': error }"
        @input="onInput"
        @keydown="onKeydown"
        @focus="onFocus"
        @blur="onBlur"
      />

      <!-- Clear button -->
      <button
        v-if="selectedItem && !disabled"
        type="button"
        class="absolute inset-y-0 right-2 flex items-center text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-300"
        aria-label="Hapus pilihan"
        @mousedown.prevent="clearSelection"
      >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6L6 18M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </button>

      <!-- Loading / chevron indicator -->
      <span
        v-else
        class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-slate-400"
      >
        <svg v-if="searching" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
        </svg>
        <svg v-else class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </span>
    </div>

    <!-- Error message -->
    <p v-if="error" class="mt-1 text-xs text-red-500">{{ error }}</p>

    <!-- Hint -->
    <p v-else-if="hint" class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ hint }}</p>

    <!-- Dropdown -->
    <Transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="scale-95 opacity-0"
      enter-to-class="scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="scale-100 opacity-100"
      leave-to-class="scale-95 opacity-0"
    >
      <ul
        v-if="isOpen && (results.length > 0 || showEmpty)"
        :id="listId"
        role="listbox"
        class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-xl border border-slate-200 bg-white py-1 shadow-lg
               dark:border-slate-700 dark:bg-slate-900"
      >
        <!-- Results -->
        <li
          v-for="(item, index) in results"
          :id="optionId(index)"
          :key="getKey(item)"
          role="option"
          :aria-selected="activeIndex === index"
          class="flex cursor-pointer items-center gap-2 px-3 py-2 text-sm transition"
          :class="activeIndex === index
            ? 'bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-300'
            : 'text-slate-800 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800'"
          @mousedown.prevent="selectItem(item)"
        >
          <!-- Custom slot or default render -->
          <slot name="item" :item="item">
            <span>{{ getLabel(item) }}</span>
          </slot>
        </li>

        <!-- Empty state -->
        <li
          v-if="results.length === 0 && showEmpty"
          class="px-3 py-2 text-sm text-slate-500 dark:text-slate-400"
          role="option"
          aria-selected="false"
        >
          {{ emptyText }}
        </li>
      </ul>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onBeforeUnmount } from 'vue'

// ── Props ────────────────────────────────────────────────────────────────────
const props = defineProps({
  modelValue: { type: [Object, String, Number, null], default: null },
  /** Fungsi async (query) => item[] — dipanggil saat user mengetik */
  searchFn:   { type: Function, required: true },
  /** Nama field untuk label tampilan item. Bisa fungsi (item) => string */
  labelKey:   { type: [String, Function], default: 'name' },
  /** Nama field untuk key unik item */
  valueKey:   { type: String, default: 'id' },
  label:      { type: String, default: '' },
  placeholder: { type: String, default: 'Ketik untuk mencari…' },
  hint:       { type: String, default: '' },
  error:      { type: String, default: '' },
  required:   { type: Boolean, default: false },
  disabled:   { type: Boolean, default: false },
  wrapperClass: { type: String, default: '' },
  emptyText:  { type: String, default: 'Tidak ada hasil.' },
  debounceMs: { type: Number, default: 300 },
})

// ── Emits ────────────────────────────────────────────────────────────────────
const emit = defineEmits(['update:modelValue', 'select', 'clear'])

// ── Refs & state ─────────────────────────────────────────────────────────────
const inputRef    = ref(null)
const wrapperRef  = ref(null)
const query       = ref('')
const results     = ref([])
const isOpen      = ref(false)
const activeIndex = ref(-1)
const searching   = ref(false)
const showEmpty   = ref(false)
const selectedItem = ref(null)
let   debounceTimer = null

// Unique IDs for ARIA
const uid      = Math.random().toString(36).slice(2)
const inputId  = `autocomplete-input-${uid}`
const listId   = `autocomplete-list-${uid}`
const optionId = (index) => `autocomplete-option-${uid}-${index}`
const activeOptionId = computed(() =>
  activeIndex.value >= 0 ? optionId(activeIndex.value) : undefined,
)

// ── Label / key helpers ───────────────────────────────────────────────────────
function getLabel(item) {
  if (!item) return ''
  if (typeof props.labelKey === 'function') return props.labelKey(item)
  return item[props.labelKey] ?? ''
}
function getKey(item) {
  return item?.[props.valueKey] ?? Math.random()
}

// ── Watch modelValue (external set) ──────────────────────────────────────────
watch(() => props.modelValue, (val) => {
  if (val && typeof val === 'object') {
    selectedItem.value = val
    query.value = getLabel(val)
  } else if (!val) {
    selectedItem.value = null
    query.value = ''
  }
}, { immediate: true })

// ── Input handler ─────────────────────────────────────────────────────────────
function onInput() {
  selectedItem.value = null
  emit('update:modelValue', null)
  showEmpty.value = false
  clearTimeout(debounceTimer)

  if (query.value.length < 2) {
    results.value = []
    isOpen.value  = false
    return
  }

  debounceTimer = setTimeout(doSearch, props.debounceMs)
}

async function doSearch() {
  searching.value = true
  showEmpty.value = false
  try {
    results.value = await props.searchFn(query.value)
    activeIndex.value = -1
    isOpen.value  = results.value.length > 0 || true   // show empty state too
    showEmpty.value = results.value.length === 0
  } finally {
    searching.value = false
  }
}

// ── Keyboard navigation ───────────────────────────────────────────────────────
function onKeydown(e) {
  if (!isOpen.value) return

  switch (e.key) {
    case 'ArrowDown':
      e.preventDefault()
      activeIndex.value = Math.min(activeIndex.value + 1, results.value.length - 1)
      scrollActiveIntoView()
      break
    case 'ArrowUp':
      e.preventDefault()
      activeIndex.value = Math.max(activeIndex.value - 1, 0)
      scrollActiveIntoView()
      break
    case 'Enter':
      e.preventDefault()
      if (activeIndex.value >= 0 && results.value[activeIndex.value]) {
        selectItem(results.value[activeIndex.value])
      }
      break
    case 'Escape':
      closeDropdown()
      break
    case 'Tab':
      closeDropdown()
      break
  }
}

function scrollActiveIntoView() {
  nextTick(() => {
    const el = document.getElementById(optionId(activeIndex.value))
    el?.scrollIntoView({ block: 'nearest' })
  })
}

// ── Select & clear ─────────────────────────────────────────────────────────────
function selectItem(item) {
  selectedItem.value = item
  query.value = getLabel(item)
  emit('update:modelValue', item)
  emit('select', item)
  closeDropdown()
}

function clearSelection() {
  selectedItem.value = null
  query.value = ''
  results.value = []
  emit('update:modelValue', null)
  emit('clear')
  nextTick(() => inputRef.value?.focus())
}

// ── Focus / blur ──────────────────────────────────────────────────────────────
function onFocus() {
  if (results.value.length > 0) isOpen.value = true
}

function onBlur(e) {
  // Delay to allow mousedown on list items to fire first
  setTimeout(() => {
    if (!wrapperRef.value?.contains(document.activeElement)) {
      closeDropdown()
      // Restore label if selection lost
      if (!selectedItem.value) query.value = ''
    }
  }, 150)
}

function closeDropdown() {
  isOpen.value  = false
  activeIndex.value = -1
}

// ── Cleanup ────────────────────────────────────────────────────────────────────
onBeforeUnmount(() => clearTimeout(debounceTimer))
</script>
