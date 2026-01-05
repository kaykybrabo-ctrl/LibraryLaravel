<template>
  <div class="authors-page">
    <div class="page-header">
      <h2>Autores</h2>
      <div class="filters">
        <select v-model="perPage" @change="updateAuthors" class="per-page-select">
          <option value="12">12 por página</option>
          <option value="24">24 por página</option>
          <option value="48">48 por página</option>
          <option value="96">96 por página</option>
        </select>
        
        <select v-model="sortBy" @change="updateAuthors" class="sort-select">
          <option value="name_asc">Nome (A-Z)</option>
          <option value="name_desc">Nome (Z-A)</option>
          <option value="books_count">Mais Livros</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="loading-container">
      <LoadingSpinner text="Carregando autores..." />
    </div>

    <div v-else-if="authors.length === 0" class="empty-state">
      <p>Nenhum autor encontrado.</p>
    </div>

    <div v-else class="authors-grid">
      <div 
        v-for="author in sortedAuthors" 
        :key="author.id" 
        class="author-card"
        @click="$emit('authorDetail', author)"
      >
        <div class="author-photo">
          <img :src="author.photo || '/images/default-author.jpg'" :alt="author.name" />
        </div>
        <div class="author-info">
          <h3>{{ author.name }}</h3>
          <p v-if="author.bio" class="author-bio">{{ author.bio }}</p>
          <p class="books-count">{{ author.books ? author.books.length : 0 }} livros</p>
        </div>
      </div>
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
import LoadingSpinner from './LoadingSpinner.vue';

export default {
  name: 'Authors',
  components: { LoadingSpinner },
  props: {
    authors: Array,
    loading: Boolean,
    pagination: Object
  },
  data() {
    return {
      perPage: 12,
      sortBy: 'name_asc'
    }
  },
  methods: {
    updateAuthors() {
      this.$emit('update', { perPage: this.perPage, sortBy: this.sortBy });
    },
    goToPage(page) {
      this.$emit('goToPage', page);
    }
  },
  computed: {
    sortedAuthors() {
      let sorted = [...this.authors];
      
      switch (this.sortBy) {
        case 'name_asc':
          sorted.sort((a, b) => a.name.localeCompare(b.name));
          break;
        case 'name_desc':
          sorted.sort((a, b) => b.name.localeCompare(a.name));
          break;
        case 'books_count':
          sorted.sort((a, b) => (b.books ? b.books.length : 0) - (a.books ? a.books.length : 0));
          break;
      }
      
      return sorted;
    }
  }
}
</script>

<style scoped>
.authors-page {
  padding: 1rem;
}

.page-header {
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

.authors-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.author-card {
  background: white;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1rem;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.author-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.author-photo {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  margin-bottom: 1rem;
}

.author-photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.author-info h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.2rem;
  color: #333;
}

.author-bio {
  color: #666;
  font-size: 0.9rem;
  margin: 0 0 1rem 0;
  line-height: 1.4;
}

.books-count {
  color: #007bff;
  font-weight: bold;
  font-size: 0.9rem;
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
}

.page-btn:hover:not(.active) {
  background: #f8f9fa;
}
</style>
