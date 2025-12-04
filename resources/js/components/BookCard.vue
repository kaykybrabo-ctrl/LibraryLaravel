<template>
  <div class="book-card">
    <div class="book-image-container">
      <img 
        :src="book.photo || defaultImage" 
        :alt="book.title"
        class="book-image"
      />
      <div v-if="loanStatus" class="loan-badge">
        <span class="badge-text">ALUGADO POR</span>
        <span class="badge-user">{{ loanStatus.display_name }}</span>
      </div>
    </div>

    <div class="book-info">
      <h3 class="book-title">{{ book.title }}</h3>
      <p class="book-author">
        por 
        <router-link 
          :to="`/authors/${book.author.id}`"
          class="author-link"
        >
          {{ book.author.name_author }}
        </router-link>
      </p>

      <div class="book-rating" v-if="book.rating">
        <span class="stars">⭐ {{ book.rating.toFixed(1) }}</span>
      </div>

      <div class="book-actions">
        <button 
          v-if="!loanStatus && canRent"
          @click="rentBook"
          class="btn btn-primary"
          :disabled="isLoading"
        >
          {{ isLoading ? 'Alugando...' : '📖 Alugar' }}
        </button>

        <button 
          v-else-if="loanStatus && isMyLoan"
          @click="returnBook"
          class="btn btn-danger"
          :disabled="isLoading"
        >
          {{ isLoading ? 'Devolvendo...' : '↩️ Devolver' }}
        </button>

        <button 
          v-else-if="isAdmin"
          class="btn btn-disabled"
          disabled
        >
          👑 Modo Admin
        </button>

        <button 
          v-else
          class="btn btn-disabled"
          disabled
        >
          ❌ Indisponível
        </button>
      </div>

      <div v-if="loanStatus" class="loan-info">
        <p class="time-remaining">
          ⏰ {{ loanStatus.time_remaining }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useQuery, useMutation } from '@vue/apollo-composable'
import gql from 'graphql-tag'

interface Book {
  id: number
  title: string
  photo?: string
  author: {
    id: number
    name_author: string
  }
  rating?: number
}

interface Props {
  book: Book
  currentUser?: any
}

const props = defineProps<Props>()
const emit = defineEmits(['book-rented', 'book-returned'])

const isLoading = ref(false)
const defaultImage = 'data:image/svg+xml;base64,...' // SVG inline

const loanStatus = ref(null)
const isMyLoan = computed(() => {
  return loanStatus.value?.user_id === props.currentUser?.id
})
const canRent = computed(() => {
  return !props.currentUser?.isAdmin && !loanStatus.value
})
const isAdmin = computed(() => {
  return props.currentUser?.isAdmin
})

// GraphQL Queries
const RENT_BOOK = gql`
  mutation RentBook($bookId: ID!) {
    rentBook(bookId: $bookId) {
      id
      dueDate
      daysRemaining
      hoursRemaining
      timeRemaining
      user {
        id
        displayName
      }
    }
  }
`

const RETURN_BOOK = gql`
  mutation ReturnBook($loanId: ID!) {
    returnBook(loanId: $loanId) {
      id
      returnedAt
    }
  }
`

// Mutations
const { mutate: rentBookMutation } = useMutation(RENT_BOOK)
const { mutate: returnBookMutation } = useMutation(RETURN_BOOK)

const rentBook = async () => {
  try {
    isLoading.value = true
    const result = await rentBookMutation({ bookId: props.book.id })
    loanStatus.value = result.data.rentBook
    emit('book-rented', result.data.rentBook)
  } catch (error) {
    console.error('Erro ao alugar livro:', error)
  } finally {
    isLoading.value = false
  }
}

const returnBook = async () => {
  try {
    isLoading.value = true
    await returnBookMutation({ loanId: loanStatus.value.id })
    loanStatus.value = null
    emit('book-returned')
  } catch (error) {
    console.error('Erro ao devolver livro:', error)
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.book-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.book-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.book-image-container {
  position: relative;
  width: 100%;
  height: 280px;
  overflow: hidden;
  background: #f0f0f0;
}

.book-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.loan-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(220, 53, 69, 0.9);
  color: white;
  padding: 8px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: bold;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.badge-text {
  font-size: 10px;
  opacity: 0.9;
}

.badge-user {
  font-size: 11px;
  margin-top: 2px;
}

.book-info {
  padding: 20px;
}

.book-title {
  margin: 0 0 8px 0;
  font-size: 16px;
  font-weight: 600;
  color: #162c74;
  line-height: 1.3;
}

.book-author {
  margin: 0 0 12px 0;
  font-size: 14px;
  color: #666;
}

.author-link {
  color: #1976d2;
  text-decoration: none;
  font-weight: 600;
  cursor: pointer;
  transition: color 0.2s ease;
}

.author-link:hover {
  color: #0d47a1;
  text-decoration: underline;
}

.book-rating {
  margin-bottom: 12px;
}

.stars {
  font-size: 14px;
  color: #ffc107;
}

.book-actions {
  display: flex;
  gap: 10px;
  margin-bottom: 12px;
}

.btn {
  flex: 1;
  padding: 10px;
  border: none;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary {
  background: linear-gradient(135deg, #162c74, #1976d2);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(22, 44, 116, 0.3);
}

.btn-danger {
  background: linear-gradient(135deg, #dc3545, #c82333);
  color: white;
}

.btn-danger:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.btn-disabled {
  background: #e9ecef;
  color: #6c757d;
  cursor: not-allowed;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.loan-info {
  padding-top: 12px;
  border-top: 1px solid #f0f0f0;
}

.time-remaining {
  margin: 0;
  font-size: 13px;
  color: #dc3545;
  font-weight: 600;
}
</style>
