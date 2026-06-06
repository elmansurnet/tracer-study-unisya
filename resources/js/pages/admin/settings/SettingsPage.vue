<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { useSettingStore } from '@/stores/useSettingStore'

const store      = useSettingStore()
const activeTab  = ref('university')
const formValues = reactive({})

const tabList = [
  { key: 'university',    label: 'Universitas' },
  { key: 'tracer',        label: 'Tracer Study' },
  { key: 'wa_gateway',    label: 'WA Gateway' },
  { key: 'notification',  label: 'Notifikasi' },
]

onMounted(async () => {
  await store.fetchAll()
  populateForm()
})

watch(() => store.groups, () => populateForm(), { deep: true })

function populateForm() {
  Object.keys(store.groups).forEach(group => {
    store.groups[group].forEach(item => {
      const fKey = `${group}.${item.key}`
      formValues[fKey] = item.value === '[ENCRYPTED]' ? '' : item.value
    })
  })
}

const currentItems = computed(() => store.groups[activeTab.value] ?? [])

async function saveGroup() {
  const settings = currentItems.value
    .filter(item => !item.is_encrypted || formValues[`${activeTab.value}.${item.key}`] !== '')
    .map(item => ({
      group: activeTab.value,
      key:   item.key,
      value: formValues[`${activeTab.value}.${item.key}`] ?? '',
    }))
  try {
    await store.batchUpdate(settings)
  } catch {}
}

function inputType(item) {
  if (item.type === 'number') return 'number'
  if (item.key.includes('password') || item.key.includes('api_key') || item.key.includes('secret')) return 'password'
  return 'text'
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Pengaturan Aplikasi</h1>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Konfigurasi sistem Tracer Study UNISYA.</p>
    </div>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 dark:border-gray-700">
      <nav class="-mb-px flex gap-4">
        <button
          v-for="tab in tabList" :key="tab.key"
          class="pb-3 text-sm font-medium transition-colors"
          :class="activeTab === tab.key
            ? 'border-b-2 border-primary text-primary'
            : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
          @click="activeTab = tab.key; store.clearMessages()"
        >
          {{ tab.label }}
        </button>
      </nav>
    </div>

    <!-- Loading -->
    <div v-if="store.isLoading" class="flex justify-center py-12">
      <svg class="h-7 w-7 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
      </svg>
    </div>

    <!-- Form -->
    <div v-else class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
      <!-- Success / Error -->
      <div v-if="store.successMsg" class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-300">
        {{ store.successMsg }}
      </div>
      <div v-if="store.error" class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-600 dark:bg-red-900/20 dark:text-red-400">
        {{ typeof store.error === 'string' ? store.error : 'Terdapat kesalahan validasi.' }}
      </div>

      <div v-if="!currentItems.length" class="py-8 text-center text-sm text-gray-400">
        Tidak ada pengaturan untuk grup ini.
      </div>

      <form v-else @submit.prevent="saveGroup">
        <div class="space-y-5">
          <div v-for="item in currentItems" :key="item.key">
            <label :for="`setting-${item.key}`" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
              {{ item.label }}
            </label>
            <!-- Boolean toggle -->
            <div v-if="item.type === 'boolean'" class="flex items-center gap-3">
              <button
                type="button"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                :class="formValues[`${activeTab}.${item.key}`] === 'true' ? 'bg-primary' : 'bg-gray-300 dark:bg-gray-600'"
                @click="formValues[`${activeTab}.${item.key}`] = formValues[`${activeTab}.${item.key}`] === 'true' ? 'false' : 'true'"
              >
                <span
                  class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                  :class="formValues[`${activeTab}.${item.key}`] === 'true' ? 'translate-x-6' : 'translate-x-1'"
                />
              </button>
              <span class="text-sm text-gray-600 dark:text-gray-400">
                {{ formValues[`${activeTab}.${item.key}`] === 'true' ? 'Aktif' : 'Nonaktif' }}
              </span>
            </div>
            <!-- Text / Number / Password -->
            <input
              v-else
              :id="`setting-${item.key}`"
              v-model="formValues[`${activeTab}.${item.key}`]"
              :type="inputType(item)"
              :placeholder="item.is_encrypted ? 'Kosongkan jika tidak ingin mengubah' : ''"
              class="input-base w-full"
            />
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <button type="submit" class="btn-primary" :disabled="store.isSaving">
            {{ store.isSaving ? 'Menyimpan...' : 'Simpan Pengaturan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
