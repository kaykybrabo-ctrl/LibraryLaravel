<template>
  <div class="book-detail" v-if="book">
    <div class="book-header">
      <div class="book-cover">
        <img :src="book.photo || '/images/default-book.jpg'" :alt="book.title" />
      </div>
      <div class="book-info">
        <h1>{{ book.title }}</h1>
        <p class="author">por {{ book.author ? book.author.name : 'Autor desconhecido' }}</p>
        <p class="price">R$ {{ book.price ? book.price.toFixed(2) : '0.00' }}</p>
      </div>
    </div>
    
    <div class="book-description">
      <h3>Descrição</h3>
      <p>{{ book.description || 'Sem descrição disponível.' }}</p>
    </div>
    
    <div class="book-actions">
      <button 
        v-if="!book.isBorrowed" 
        @click="$emit('borrowBook', book)" 
        class="btn btn-primary"
      >
        Alugar Livro
      </button>
      
      <button 
        @click="$emit('addToCart', book)" 
        class="btn btn-secondary"
      >
        Adicionar ao Carrinho
      </button>
      
      <button @click="$emit('close')" class="btn btn-outline">
        Fechar
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BookDetail',
  props: {
    book: Object
  },
  methods: {
    borrowBook() {
      this.$emit('borrowBook', this.book);
    },
    addToCart() {
      this.$emit('addToCart', this.book);
    },
    close() {
      this.$emit('close');
    }
  }
}
</script>

<style scoped>
.book-detail {
  max-width: 800px;
  margin: 0 auto;
  background: white;
  border-radius: 8px;
  overflow: hidden;
}

.book-header {
  display: flex;
  padding: 2rem;
  gap: 2rem;
}

.book-cover {
  flex: 1;
  max-width: 200px;
}

.book-cover img {
  width: 100%;
  height: 300px;
  object-fit: cover;
  border-radius: 4px;
}

.book-info {
  flex: 2;
}

.book-info h1 {
  margin: 0 0 1rem 0;
  font-size: 2rem;
  color: #333;
}

.author {
  color: #666;
  font-size: 1rem;
  margin: 0 0 0.5rem 0;
}

.price {
  font-size: 1.5rem;
  color: #007bff;
  font-weight: bold;
}

.book-description {
  padding: 0 2rem 2rem;
}

.book-description h3 {
  margin: 0 0 1rem 0;
  color: #333;
}

.book-description p {
  line-height: 1.6;
  color: #666;
}

.book-actions {
  display: flex;
  gap: 1rem;
  padding: 2rem;
  border-top: 1px solid #eee;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-primary:hover {
  background: #0056b3;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #545b62;
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
