<template>
  <div class="book-list">
    <div class="list-header">
      <h2>{{ $t('navigation.books') }}</h2>
      
      <div class="filters">
        <select v-model="perPage" @change="updatePerPage" class="per-page-select">
          <option value="12">{{ $t('pagination.perPageOption', { n: 12 }) }}</option>
          <option value="24">{{ $t('pagination.perPageOption', { n: 24 }) }}</option>
          <option value="48">{{ $t('pagination.perPageOption', { n: 48 }) }}</option>
          <option value="96">{{ $t('pagination.perPageOption', { n: 96 }) }}</option>
        </select>
        
        <select v-model="sortBy" @change="updateSort" class="sort-select">
          <option value="created_at_desc">{{ $t('books.sortRecent') }}</option>
          <option value="created_at_asc">{{ $t('books.sortOldest') }}</option>
          <option value="title_asc">{{ $t('books.sortTitleAsc') }}</option>
          <option value="title_desc">{{ $t('books.sortTitleDesc') }}</option>
          <option value="price_asc">{{ $t('books.sortPriceAsc') }}</option>
          <option value="price_desc">{{ $t('books.sortPriceDesc') }}</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="loading-container">
      <LoadingSpinner :text="$t('books.loading')" />
    </div>

    <div v-else-if="books.length === 0" class="empty-state">
      <p>{{ $t('books.noBooksFound') }}</p>
    </div>

    <div v-else class="books-grid">
      <BookCard
        v-for="book in books"
        :key="book.id"
        :book="book"
        @view="$emit('view', $event)"
        @borrow="$emit('borrow', $event)"
        @addToCart="$emit('addToCart', $event)"
        @toggleFavorite="$emit('toggleFavorite', $event)"
      />
    </div>

    <div v-if="pagination" class="pagination">
      <button 
        v-for="page in pagination.last_page"
        :key="page"
        :class="['page-btn', { active: pagination.current_page === page }]"
        @click="goToPage(page)"
      >
        {{ page }}
      </button>
    </div>
  </div>
</template>

<script>
import BookCard from './BookCard.vue'
import LoadingSpinner from '../common/LoadingSpinner.vue'

export default {
  name: 'BookList',
  components: { BookCard, LoadingSpinner },
  props: {
    books: Array,
    loading: Boolean,
    pagination: Object
  },
  data() {
    return {
      perPage: 12,
      sortBy: 'created_at_desc'
    }
  },
  methods: {
    updatePerPage() {
      this.$emit('update:perPage', this.perPage)
    },
    updateSort() {
      this.$emit('update:sortBy', this.sortBy)
    },
    goToPage(page) {
      this.$emit('goToPage', page)
    }
  }
}
</script>

<style scoped>
.book-list {
  padding: 1rem;
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.filters {
  display: flex;
  gap: 1rem;
}

.per-page-select,
.sort-select {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: white;
}

.loading-container,
.empty-state {
  text-align: center;
  padding: 2rem;
}

.books-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.pagination {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
}

.page-btn {
  padding: 0.5rem 0.75rem;
  border: 1px solid #ddd;
  background: white;
  cursor: pointer;
  border-radius: 4px;
}

.page-btn.active {
  background: #007bff;
  color: white;
  border-color: #007bff;
}

.page-btn:hover:not(.active) {
  background: #f8f9fa;
}
</style>
