<template>
  <div class="space-y-5">
    <!-- Header -->
    <div>
      <h1 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Riwayat Pekerjaan</h1>
      <p class="text-sm text-slate-500 dark:text-slate-400">Kelola data riwayat pekerjaan Anda</p>
    </div>

    <!-- Employment list menggunakan AlumniEmploymentTab dengan context self -->
    <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
      <AlumniEmploymentTabSelf />
    </div>
  </div>
</template>

<script setup>
import { defineComponent, h, onMounted, ref } from 'vue'
import { useAlumniStore }    from '@/stores/useAlumniStore'
import AppButton             from '@/components/base/AppButton.vue'
import AppSkeleton           from '@/components/base/AppSkeleton.vue'
import AppBadge              from '@/components/base/AppBadge.vue'
import AppPagination         from '@/components/base/AppPagination.vue'
import AppConfirm            from '@/components/base/AppConfirm.vue'
import EmploymentFormModal   from '@/components/alumni/EmploymentFormModal.vue'

/**
 * AlumniEmploymentTabSelf — versi self context dari AlumniEmploymentTab.
 * Dibuat inline karena hanya berbeda pada store methods & endpoint (self vs admin).
 */
const AlumniEmploymentTabSelf = defineComponent({
  name: 'AlumniEmploymentTabSelf',
  setup() {
    const store        = useAlumniStore()
    const modalOpen    = ref(false)
    const editingHistory = ref(null)
    const confirmOpen  = ref(false)
    const deletingItem = ref(null)
    const deleting     = ref(false)

    const monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']
    function formatDate(d) {
      if (!d) return null
      const dt = new Date(d)
      return `${monthNames[dt.getMonth()]} ${dt.getFullYear()}`
    }
    function formatPeriod(item) {
      const start = formatDate(item.start_date)
      if (item.is_current) return `${start} – Sekarang`
      const end = formatDate(item.end_date)
      return end ? `${start} – ${end}` : (start ?? '—')
    }
    const typeLabels = { full_time:'Full Time', part_time:'Part Time', freelance:'Freelance', internship:'Magang', contract:'Kontrak', entrepreneur:'Wirausaha', other:'Lainnya' }
    function formatType(t) { return typeLabels[t] ?? t }

    function openCreate() { editingHistory.value = null; modalOpen.value = true }
    function openEdit(item) { editingHistory.value = item; modalOpen.value = true }
    async function onSaved() { modalOpen.value = false; await store.fetchMyEmploymentHistories() }
    function confirmDelete(item) { deletingItem.value = item; confirmOpen.value = true }
    async function doDelete() {
      deleting.value = true
      try { await store.deleteMyEmploymentHistory(deletingItem.value.id); confirmOpen.value = false }
      finally { deleting.value = false }
    }
    async function restoreItem(item) {
      await store.restoreMyEmploymentHistory(item.id)
      await store.fetchMyEmploymentHistories()
    }
    async function onPageChange(page) { await store.fetchMyEmploymentHistories({ page }) }

    onMounted(() => store.fetchMyEmploymentHistories())

    return {
      store, modalOpen, editingHistory, confirmOpen, deletingItem, deleting,
      openCreate, openEdit, onSaved, confirmDelete, doDelete, restoreItem, onPageChange,
      formatPeriod, formatType,
    }
  },
  render() {
    const { store } = this
    return h('div', { class: 'space-y-4' }, [
      // Header
      h('div', { class: 'flex items-center justify-between' }, [
        h('div', {}, [
          h('p', { class: 'text-sm font-semibold text-slate-800 dark:text-slate-200' }, 'Riwayat Pekerjaan Saya'),
          h('p', { class: 'text-xs text-slate-500' }, `${store.employmentMeta.total} entri`),
        ]),
        h(AppButton, { variant: 'primary', size: 'sm', onClick: this.openCreate }, () => '+ Tambah Pekerjaan'),
      ]),

      // List
      store.loading
        ? h('div', { class: 'space-y-3' }, [1,2,3].map(i =>
            h('div', { key: i, class: 'flex gap-3' }, [
              h(AppSkeleton, { class: 'h-10 w-10 rounded-full' }),
              h('div', { class: 'flex-1 space-y-2' }, [
                h(AppSkeleton, { class: 'h-4 w-2/3' }),
                h(AppSkeleton, { class: 'h-3 w-1/3' }),
              ]),
            ])
          ))
        : store.employmentHistories.length === 0
          ? h('div', { class: 'flex flex-col items-center gap-3 rounded-2xl border-2 border-dashed border-slate-200 py-10 text-center dark:border-slate-700' }, [
              h('p', { class: 'text-sm text-slate-500' }, 'Belum ada riwayat pekerjaan.'),
            ])
          : h('div', { class: 'space-y-3' }, store.employmentHistories.map(item =>
              h('div', {
                key: item.id,
                class: `flex gap-4 rounded-2xl border p-4 shadow-sm transition dark:border-slate-700 dark:bg-slate-900 ${
                  item.deleted_at ? 'border-red-100 bg-red-50/30' : 'border-slate-200 bg-white'
                }`,
              }, [
                h('div', { class: 'min-w-0 flex-1' }, [
                  h('p', { class: 'text-sm font-semibold text-slate-800 dark:text-slate-200' }, item.position),
                  h('p', { class: 'mt-0.5 text-xs text-slate-500' }, `${item.institution?.name ?? '—'} · ${item.profession?.name ?? '—'}`),
                  h('p', { class: 'mt-0.5 text-xs text-slate-400' }, `${this.formatPeriod(item)}${item.employment_type ? ' · ' + this.formatType(item.employment_type) : ''}`),
                ]),
                h('div', { class: 'flex shrink-0 items-center gap-1' }, [
                  !item.deleted_at
                    ? [
                        h('button', { class: 'rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800', title: 'Edit', onClick: () => this.openEdit(item) },
                          h('svg', { class:'h-4 w-4', viewBox:'0 0 24 24', fill:'none', stroke:'currentColor', strokeWidth:'2' },
                            h('path', { d:'M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z', strokeLinecap:'round', strokeLinejoin:'round' })
                          )
                        ),
                        h('button', { class: 'rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-500', title: 'Hapus', onClick: () => this.confirmDelete(item) },
                          h('svg', { class:'h-4 w-4', viewBox:'0 0 24 24', fill:'none', stroke:'currentColor', strokeWidth:'2' },
                            h('polyline', { points:'3 6 5 6 21 6', strokeLinecap:'round' }),
                            h('path', { d:'M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2', strokeLinecap:'round' })
                          )
                        ),
                      ]
                    : h('button', { class: 'rounded-lg p-1.5 text-emerald-500 hover:bg-emerald-50', title: 'Pulihkan', onClick: () => this.restoreItem(item) },
                        h('svg', { class:'h-4 w-4', viewBox:'0 0 24 24', fill:'none', stroke:'currentColor', strokeWidth:'2' },
                          h('path', { d:'M3 12a9 9 0 105.168-8.185M3 3v5h5', strokeLinecap:'round', strokeLinejoin:'round' })
                        )
                      ),
                ]),
              ])
            )),

      // Pagination
      store.employmentMeta.last_page > 1
        ? h(AppPagination, { currentPage: store.employmentMeta.current_page, lastPage: store.employmentMeta.last_page, onChange: this.onPageChange })
        : null,

      // Modal
      h(EmploymentFormModal, { show: this.modalOpen, history: this.editingHistory, context: 'self', onClose: () => { this.modalOpen = false }, onSaved: this.onSaved }),

      // Confirm
      h(AppConfirm, {
        show: this.confirmOpen,
        title: 'Hapus Riwayat Pekerjaan?',
        message: `Data pekerjaan di '${this.deletingItem?.institution?.name ?? ''}' akan diarsipkan.`,
        confirmText: 'Hapus', variant: 'danger', loading: this.deleting,
        onConfirm: this.doDelete, onCancel: () => { this.confirmOpen = false },
      }),
    ])
  },
})
</script>
