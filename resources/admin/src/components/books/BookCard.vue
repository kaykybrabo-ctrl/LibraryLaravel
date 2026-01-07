<template>
  <div class="book-card">
    <div class="book-cover">
      <img
        :src="thumb ? thumb(book.photo, 600, 360, 'book') : book.photo"
        :alt="book.title"
        @error="$event.target.src = (thumb ? thumb('', 600, 360, 'book') : 'https://res.cloudinary.com/ddfgsoh5g/image/upload/v1761062932/pedbook/books/default-book.svg')"
        loading="lazy"
      />
    </div>
    
    <div class="book-info">
      <h3 class="book-title">{{ book.title }}</h3>
      <p class="book-author">{{ book.author?.name }}</p>
      <p class="book-price">{{ $formatCurrency(Number(book.price || 0)) }}</p>
      
      <div class="book-actions">
        <button class="btn btn-sm btn-outline" @click="$emit('view', book)">
          {{ $t('books.details') }}
        </button>
        
        <button 
          v-if="!book.isBorrowed" 
          class="btn btn-sm btn-primary"
          @click="$emit('borrow', book)"
        >
          {{ $t('books.rent') }}
        </button>
        
        <button 
          class="btn btn-sm btn-secondary"
          @click="$emit('addToCart', book)"
        >
          {{ $t('books.addToCart') }}
        </button>
        
        <button 
          class="btn btn-sm btn-outline"
          @click="$emit('toggleFavorite', book)"
        >
          {{ book.isFavorited ? $t('books.removeFromFavorites') : $t('books.addToFavorites') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BookCard',
  props: {
    book: {
      type: Object,
      required: true
    },
    thumb: {
      type: Function,
      default: null,
    },
  },
  emits: ['view', 'borrow', 'addToCart', 'toggleFavorite']
}
</script>

<style scoped>
.book-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
  background: white;
  transition: transform 0.2s, box-shadow 0.2s;
}

.book-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.book-cover {
  height: 200px;
  overflow: hidden;
}

.book-cover img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.book-info {
  padding: 1rem;
}

.book-title {
  font-size: 1.1rem;
  margin: 0 0 0.5rem 0;
  line-height: 1.3;
}

.book-author {
  color: #666;
  margin: 0 0 0.5rem 0;
  font-size: 0.9rem;
}

.book-price {
  font-weight: bold;
  color: #007bff;
  margin: 0 0 1rem 0;
}

.book-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.btn {
  padding: 0.5rem 1rem;
  border: 1px solid;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.8rem;
  text-decoration: none;
  display: inline-block;
  transition: all 0.2s;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

.btn-primary {
  background: #007bff;
  color: white;
  border-color: #007bff;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  border-color: #6c757d;
}

.btn-outline {
  background: transparent;
  color: #007bff;
  border-color: #007bff;
}

.btn-outline:hover {
  background: #007bff;
  color: white;
}
</style>
