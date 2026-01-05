<template>
  <div class="author-detail" v-if="author">
    <div class="author-header">
      <div class="author-photo">
        <img :src="author.photo || '/images/default-author.jpg'" :alt="author.name" />
      </div>
      <div class="author-info">
        <h1>{{ author.name }}</h1>
        <p v-if="author.bio" class="author-bio">{{ author.bio }}</p>
        <p class="books-count">{{ author.books ? author.books.length : 0 }} livros publicados</p>
      </div>
    </div>
    
    <div class="author-books" v-if="author.books && author.books.length > 0">
      <h3>Livros de {{ author.name }}</h3>
      <div class="books-grid">
        <div 
          v-for="book in author.books" 
          :key="book.id" 
          class="book-item"
          @click="$emit('bookDetail', book)"
        >
          <img :src="book.photo || '/images/default-book.jpg'" :alt="book.title" />
          <h4>{{ book.title }}</h4>
          <p class="book-price">R$ {{ book.price ? book.price.toFixed(2) : '0.00' }}</p>
        </div>
      </div>
    </div>
    
    <div class="author-actions">
      <button @click="$emit('close')" class="btn btn-outline">
        Fechar
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AuthorDetail',
  props: {
    author: Object
  },
  methods: {
    close() {
      this.$emit('close');
    }
  }
}
</script>

<style scoped>
.author-detail {
  max-width: 800px;
  margin: 0 auto;
  background: white;
  border-radius: 8px;
  overflow: hidden;
}

.author-header {
  display: flex;
  padding: 2rem;
  gap: 2rem;
  align-items: center;
}

.author-photo {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  overflow: hidden;
}

.author-photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.author-info {
  flex: 1;
}

.author-info h1 {
  margin: 0 0 1rem 0;
  font-size: 2rem;
  color: #333;
}

.author-bio {
  color: #666;
  font-size: 1rem;
  margin: 0 0 1rem 0;
  line-height: 1.6;
}

.books-count {
  color: #007bff;
  font-weight: bold;
  font-size: 1.1rem;
}

.author-books {
  padding: 0 2rem;
}

.author-books h3 {
  margin: 0 0 1rem 0;
  color: #333;
}

.books-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
}

.book-item {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 4px;
  padding: 1rem;
  cursor: pointer;
  transition: all 0.3s;
}

.book-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.book-item img {
  width: 100%;
  height: 120px;
  object-fit: cover;
  border-radius: 4px;
  margin-bottom: 0.5rem;
}

.book-item h4 {
  margin: 0;
  font-size: 1rem;
  color: #333;
}

.book-price {
  font-weight: bold;
  color: #007bff;
}

.author-actions {
  padding: 2rem;
  text-align: center;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
}

.btn-outline {
  background: transparent;
  color: #6c757d;
  border: 1px solid #6c757d;
}

.btn-outline:hover {
  background: #6c757d;
  color: white;
}
</style>
