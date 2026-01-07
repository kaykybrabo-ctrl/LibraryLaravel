<template>
  <Modal :show="show" :title="title || $t('common.confirmActionTitle')" @close="$emit('cancel')">
    <p>{{ message }}</p>
    
    <template #footer>
      <button class="btn btn-secondary" @click="$emit('cancel')">
        {{ cancelText || $t('common.cancel') }}
      </button>
      <button class="btn btn-danger" @click="$emit('confirm')">
        {{ confirmText || $t('common.confirm') }}
      </button>
    </template>
  </Modal>
</template>

<script>
import Modal from '../common/Modal.vue'

export default {
  name: 'ConfirmModal',
  components: { Modal },
  props: {
    show: Boolean,
    title: {
      type: String,
      default: ''
    },
    message: {
      type: String,
      required: true
    },
    confirmText: {
      type: String,
      default: ''
    },
    cancelText: {
      type: String,
      default: ''
    }
  },
  emits: ['confirm', 'cancel']
}
</script>

<style scoped>
.btn-danger {
  background: #dc3545;
  color: white;
  border-color: #dc3545;
}

.btn-danger:hover {
  background: #c82333;
}
</style>
