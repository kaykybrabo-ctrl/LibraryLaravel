<template>
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h2>{{ $t('loans.adminTitle') }}</h2>
    </div>
    <div v-if="allLoansLoading" class="text-center">
      <LoadingSpinner :text="$t('loans.loadingAdmin')" />
    </div>
    <div v-else>
      <div style="margin-bottom: 12px; max-width: 320px;">
        <select :value="adminLoansFilter" @change="$emit('update:admin-loans-filter', $event.target.value)" style="width:100%; padding:8px; border:1px solid #dee2e6; border-radius:6px;">
          <option value="all">{{ $t('loans.filterAll') }}</option>
          <option value="active">{{ $t('loans.filterActive') }}</option>
          <option value="overdue">{{ $t('loans.filterOverdue') }}</option>
          <option value="returned">{{ $t('loans.filterReturned') }}</option>
        </select>
      </div>

      <div v-if="filteredAdminLoans.length > 0">
        <div v-for="l in filteredAdminLoans" :key="l.id" style="padding: 12px 16px; border-bottom: 1px solid #eee; background: white; border-radius: 8px; margin-bottom: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
          <div style="display:flex; justify-content: space-between; align-items: center;">
            <div>
              <div style="font-weight:600; color:#162c74;">{{ l.book && l.book.title ? l.book.title : '' }}</div>
              <div style="color:#666; font-size:0.9rem;">
                {{ $t('loans.userLabel') }}
                <span v-if="l.user" @click.stop="$emit('open-user-profile', l.user)" style="cursor:pointer;color:#162c74;text-decoration:underline;">{{ l.user.name }}</span>
                <span v-else>{{ $t('common.unknown') }}</span>
                • {{ $t('loans.borrowedAt') }} {{ formatDate(l.loan_date) }} • {{ $t('loans.dueAt') }} {{ formatDate(l.return_date) }}
              </div>
            </div>
            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:6px; min-width: 160px;">
              <span v-if="l.status === 'returned'" style="display:inline-block; padding:3px 8px; border-radius:999px; background:#d4edda; color:#155724; font-size:0.8rem; font-weight:600;">{{ $t('loans.statusReturned') }}</span>
              <span v-else-if="l.status === 'overdue'" style="display:inline-block; padding:3px 8px; border-radius:999px; background:#f8d7da; color:#721c24; font-size:0.8rem; font-weight:600;">{{ $t('loans.statusOverdue') }}</span>
              <span v-else style="display:inline-block; padding:3px 8px; border-radius:999px; background:#fff3cd; color:#856404; font-size:0.8rem; font-weight:600;">{{ $t('loans.statusActive') }}</span>
              <div v-if="typeof l.days_remaining === 'number' && !isNaN(l.days_remaining)" style="font-size:0.8rem; color:#666;">
                {{ l.days_remaining >= 0 ? $t('loans.daysRemainingText', { n: l.days_remaining }) : $t('loans.daysOverdueText', { n: Math.abs(l.days_remaining) }) }}
              </div>
              <div style="display:flex; gap:8px;">
                <button v-if="l.status === 'active' || l.status === 'overdue'" class="btn btn-small" type="button" @click="$emit('return-book', l.id)">{{ $t('books.return') }}</button>
                <button class="btn btn-small btn-danger" type="button" @click="$emit('delete-loan', l.id)">{{ $t('common.delete') }}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <p v-else style="color:#666;">{{ $t('loans.noLoansForFilter') }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LoansPage',
  props: {
    allLoansLoading: { type: Boolean, required: true },
    adminLoansFilter: { type: String, required: true },
    filteredAdminLoans: { type: Array, required: true },
    formatDate: { type: Function, required: true },
  },
};
</script>
