<template>
  <div class="loans-page">
    <div class="page-header">
      <h2>{{ $t('loans.title') }}</h2>
    </div>

    <div v-if="loading" class="loading-container">
      <LoadingSpinner :text="$t('loans.loadingAdmin')" />
    </div>

    <div v-else-if="loans.length === 0" class="empty-state">
      <p>{{ $t('loans.noActiveLoans') }}</p>
    </div>

    <div v-else class="loans-list">
      <div 
        v-for="loan in loans" 
        :key="loan.id" 
        class="loan-item"
        :class="{ overdue: loan.is_overdue }"
      >
        <div class="loan-book">
          <img :src="loan.book.photo || '/images/default-book.jpg'" :alt="loan.book.title" />
          <div class="book-info">
            <h4>{{ loan.book.title }}</h4>
            <p class="author">{{ loan.book.author ? loan.book.author.name : $t('books.unknownAuthor') }}</p>
          </div>
        </div>
        
        <div class="loan-details">
          <div class="loan-dates">
            <p><strong>{{ $t('loans.loanDate') }}:</strong> {{ formatDate(loan.loan_date) }}</p>
            <p><strong>{{ $t('loans.returnDate') }}:</strong> {{ formatDate(loan.return_date) }}</p>
            <p v-if="loan.returned_at" class="returned-date"><strong>{{ $t('loans.returnedAt') }}:</strong> {{ formatDate(loan.returned_at) }}</p>
          </div>
          
          <div class="loan-status" :class="loan.status">
            <span class="status-label">{{ getStatusText(loan.status) }}</span>
            <span v-if="loan.is_overdue" class="overdue-label">{{ $t('loans.statusOverdue') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LoadingSpinner from './LoadingSpinner.vue';

export default {
  name: 'Loans',
  components: { LoadingSpinner },
  props: {
    loans: Array,
    loading: Boolean
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return '';
      return this.$formatDate(dateString);
    },
    getStatusText(status) {
      const statusMap = {
        active: this.$t('loans.statusActive'),
        returned: this.$t('loans.statusReturned'),
        overdue: this.$t('loans.statusOverdue'),
      };
      return statusMap[status] || status;
    }
  }
}
</script>

<style scoped>
.loans-page {
  padding: 1rem;
}

.page-header {
  margin-bottom: 2rem;
}

.loading-container,
.empty-state {
  text-align: center;
  padding: 2rem;
}

.loans-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.loan-item {
  background: white;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1.5rem;
  display: flex;
  gap: 1.5rem;
}

.loan-item.overdue {
  border-color: #dc3545;
  background: #fff5f5;
}

.loan-book {
  flex: 1;
  display: flex;
  gap: 1rem;
}

.loan-book img {
  width: 80px;
  height: 120px;
  object-fit: cover;
  border-radius: 4px;
}

.book-info h4 {
  margin: 0 0 0.5rem 0;
  font-size: 1.1rem;
  color: #333;
}

.author {
  color: #666;
  font-size: 0.9rem;
}

.loan-details {
  flex: 2;
}

.loan-dates p {
  margin: 0 0 0.5rem 0;
  font-size: 0.9rem;
}

.loan-dates strong {
  color: #333;
}

.loan-status {
  margin-top: 1rem;
}

.status-label {
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: bold;
}

.status-label.active {
  background: #28a745;
  color: white;
}

.status-label.returned {
  background: #6c757d;
  color: white;
}

.status-label.overdue {
  background: #dc3545;
  color: white;
}

.overdue-label {
  background: #ff6b6b;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
  margin-left: 0.5rem;
}

.returned-date {
  color: #28a745;
  font-weight: bold;
}
</style>
