<template>
  <div class="rent-modal">
    <div class="modal-header">
      <h3>Alugar Livro</h3>
      <button @click="$emit('close')" class="close-btn">&times;</button>
    </div>
    
    <div class="rent-content" v-if="book">
      <div class="book-info">
        <img :src="book.photo || '/images/default-book.jpg'" :alt="book.title" class="book-cover" />
        <div class="book-details">
          <h4>{{ book.title }}</h4>
          <p class="author">por {{ book.author ? book.author.name : 'Autor desconhecido' }}</p>
          <p class="description">{{ book.description || 'Sem descrição disponível.' }}</p>
        </div>
      </div>
      
      <div class="rent-form">
        <div class="form-group">
          <label for="return-date">Data de Devolução</label>
          <input 
            id="return-date"
            v-model="returnDate" 
            type="date" 
            :min="minDate"
            required
          />
        </div>
        
        <div class="form-group">
          <label for="notes">Observações</label>
          <textarea 
            id="notes"
            v-model="notes" 
            placeholder="Observações opcionais..."
            rows="3"
          ></textarea>
        </div>
      </div>
      
      <div class="rent-actions">
        <button @click="$emit('close')" class="btn btn-outline">Cancelar</button>
        <button @click="confirmRent" class="btn btn-primary">Confirmar Aluguel</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RentModal',
  props: {
    book: Object
  },
  data() {
    return {
      returnDate: '',
      notes: ''
    }
  },
  computed: {
    minDate() {
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      return tomorrow.toISOString().split('T')[0];
    }
  },
  methods: {
    confirmRent() {
      const rentData = {
        book: this.book,
        returnDate: this.returnDate,
        notes: this.notes
      };
      
      this.$emit('confirmRent', rentData);
      this.close();
    },
    close() {
      this.$emit('close');
    }
  }
}
</script>

<style scoped>
.rent-modal {
  background: white;
  border-radius: 8px;
  max-width: 500px;
  margin: 0 auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #eee;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
}

.rent-content {
  padding: 1rem;
}

.book-info {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.book-cover {
  width: 100px;
  height: 150px;
  object-fit: cover;
  border-radius: 4px;
}

.book-details h4 {
  margin: 0 0 0.5rem 0;
  font-size: 1.2rem;
  color: #333;
}

.author {
  color: #666;
  font-size: 0.9rem;
  margin: 0 0 0.5rem 0;
}

.description {
  color: #666;
  line-height: 1.5;
}

.rent-form {
  border-top: 1px solid #eee;
  padding-top: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #333;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.rent-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-outline {
  background: transparent;
  color: #007bff;
  border: 1px solid #007bff;
}

.btn:hover {
  opacity: 0.8;
}
</style>
