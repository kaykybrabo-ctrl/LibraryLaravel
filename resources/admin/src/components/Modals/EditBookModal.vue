<template>
  <div class="modal" :class="{ active: show }" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3 style="color:#162c74; font-weight:600;">{{ $t('modals.editBook.title') }}</h3>
        <button class="modal-close" @click="$emit('close')">&times;</button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="$emit('submit')">
          <div class="form-group">
            <label>{{ $t('books.title') }}:</label>
            <input type="text" v-model="editBook.title" required>
          </div>
          <div class="form-group">
            <label>{{ $t('books.author') }}:</label>
            <select v-model="editBook.author_id" required>
              <option v-for="author in authors" :key="author.id" :value="author.id">{{ author.name }}</option>
            </select>
          </div>
          <div class="form-group">
            <label>{{ $t('books.description') }}:</label>
            <textarea v-model="editBook.description" rows="4" required></textarea>
          </div>
          <div class="form-group">
            <label>{{ $t('modals.editBook.priceLabel') }}</label>
            <input type="number" step="0.01" min="0" v-model.number="editBook.price">
          </div>
          <div class="form-group">
            <label>{{ $t('modals.editBook.bookPhotoLabel') }}</label>
            <input type="text" v-model="editBook.photo" required>
            <div class="mt-3">
              <button type="button" class="btn btn-small" @click="$emit('upload', 'book_edit')">{{ $t('common.upload') }}</button>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">{{ $t('common.save') }}</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EditBookModal',
  props: {
    show: { type: Boolean, required: true },
    authors: { type: Array, required: true },
    editBook: { type: Object, required: true },
  },
};
</script>
