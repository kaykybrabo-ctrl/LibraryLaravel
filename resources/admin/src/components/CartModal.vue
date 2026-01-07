<template>
  <div class="cart-modal">
    <div class="modal-header">
      <h3>{{ $t('cart.title') }}</h3>
      <button @click="$emit('close')" class="close-btn">&times;</button>
    </div>
    
    <div class="cart-content">
      <div v-if="items.length === 0" class="empty-cart">
        <p>{{ $t('cart.empty') }}</p>
      </div>
      
      <div v-else class="cart-items">
        <div 
          v-for="item in items" 
          :key="item.id" 
          class="cart-item"
        >
          <div class="item-image">
            <img :src="item.book.photo || '/images/default-book.jpg'" :alt="item.book.title" />
          </div>
          <div class="item-details">
            <h4>{{ item.book.title }}</h4>
            <p class="author">{{ item.book.author ? item.book.author.name : $t('books.unknownAuthor') }}</p>
            <p class="price">{{ $formatCurrency(Number(item.book.price || 0)) }}</p>
            <p class="quantity">{{ $t('cart.quantity') }}: {{ item.quantity }}</p>
            <p class="subtotal">{{ $t('cart.subtotal') }}: {{ $formatCurrency(Number(item.book.price || 0) * Number(item.quantity || 0)) }}</p>
          </div>
          <div class="item-actions">
            <button @click="updateQuantity(item, item.quantity - 1)" class="quantity-btn">-</button>
            <button @click="updateQuantity(item, item.quantity + 1)" class="quantity-btn">+</button>
            <button @click="removeItem(item)" class="remove-btn">{{ $t('cart.removeItem') }}</button>
          </div>
        </div>
      </div>
      
      <div class="cart-summary">
        <div class="summary-row">
          <span>{{ $t('cart.totalItems') }}: {{ totalItems }}</span>
        </div>
        <div class="summary-row total">
          <span>{{ $t('cart.total') }}: {{ $formatCurrency(Number(totalPrice || 0)) }}</span>
        </div>
      </div>
      
      <div class="cart-actions">
        <button @click="clearCart" class="btn btn-outline">{{ $t('cart.clear') }}</button>
        <button @click="checkout" class="btn btn-primary">{{ $t('cart.checkout') }}</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CartModal',
  props: {
    items: Array
  },
  methods: {
    updateQuantity(item, newQuantity) {
      if (newQuantity <= 0) {
        this.$emit('removeItem', item);
      } else {
        this.$emit('updateQuantity', { item, quantity: newQuantity });
      }
    },
    removeItem(item) {
      this.$emit('removeItem', item);
    },
    clearCart() {
      this.$emit('clearCart');
    },
    checkout() {
      this.$emit('checkout');
    },
    close() {
      this.$emit('close');
    }
  },
  computed: {
    totalItems() {
      return this.items.reduce((sum, item) => sum + item.quantity, 0);
    },
    totalPrice() {
      return this.items.reduce((sum, item) => sum + (item.book.price * item.quantity), 0);
    }
  }
}
</script>

<style scoped>
.cart-modal {
  background: white;
  border-radius: 8px;
  max-width: 600px;
  margin: 0 auto;
  max-height: 80vh;
  overflow-y: auto;
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

.cart-content {
  padding: 1rem;
}

.empty-cart {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.cart-items {
  max-height: 400px;
  overflow-y: auto;
}

.cart-item {
  display: flex;
  padding: 1rem;
  border-bottom: 1px solid #eee;
  gap: 1rem;
}

.item-image {
  width: 60px;
  height: 80px;
  object-fit: cover;
  border-radius: 4px;
}

.item-details {
  flex: 1;
}

.item-details h4 {
  margin: 0 0 0.5rem 0;
  font-size: 1rem;
  color: #333;
}

.author {
  color: #666;
  font-size: 0.9rem;
  margin: 0 0 0.5rem 0;
}

.price {
  font-weight: bold;
  color: #007bff;
  font-size: 1.1rem;
  margin: 0 0 0.5rem 0;
}

.quantity {
  color: #666;
  font-size: 0.9rem;
}

.subtotal {
  font-weight: bold;
  color: #333;
  margin: 0 0 0.5rem 0;
}

.item-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.quantity-btn {
  width: 30px;
  height: 30px;
  border: 1px solid #ddd;
  background: white;
  cursor: pointer;
  border-radius: 4px;
}

.remove-btn {
  padding: 0.25rem 0.5rem;
  border: 1px solid #dc3545;
  background: #dc3545;
  color: white;
  border-radius: 4px;
  font-size: 0.8rem;
  cursor: pointer;
}

.cart-summary {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #eee;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  font-size: 1rem;
}

.total {
  font-weight: bold;
  font-size: 1.2rem;
  color: #007bff;
}

.cart-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
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
