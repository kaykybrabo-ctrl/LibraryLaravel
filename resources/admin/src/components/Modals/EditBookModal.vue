<template>
  <div class="modal" :class="{ active: show }" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3 style="color:#162c74; font-weight:600;">{{ $t('modals.editBook.title') }}</h3>
        <button class="modal-close" @click="$emit('close')">&times;</button>
      </div>
      <div class="modal-body">
        <div v-if="editBookError" class="error" style="margin-bottom:10px;">{{ editBookError }}</div>
        <form @submit.prevent="$emit('submit')">
          <div class="form-group">
            <label>{{ $t('books.title') }}:</label>
            <input type="text" v-model="editBook.title" required>
          </div>

          <div class="modal-section">
            <div class="modal-section-title">{{ $t('modals.createBook.authorSectionTitle') }}</div>
            <div class="modal-section-helper">{{ $t('modals.createBook.authorSectionHelper') }}</div>
            <div style="margin-bottom: 10px; font-size: 0.9rem; display:flex; flex-wrap:wrap; gap:12px;">
              <label>
                <input
                  type="radio"
                  value="existing"
                  :checked="editBookAuthorMode === 'existing'"
                  @change="$emit('update:editBookAuthorMode', 'existing'); $emit('clearError')"
                >
                {{ $t('modals.createBook.selectExistingAuthor') }}
              </label>
              <label>
                <input
                  type="radio"
                  value="new"
                  :checked="editBookAuthorMode === 'new'"
                  @change="$emit('update:editBookAuthorMode', 'new'); $emit('clearError')"
                >
                {{ $t('modals.createBook.createNewAuthorWithBook') }}
              </label>
            </div>

            <div v-if="editBookAuthorMode === 'existing'">
              <div class="form-group" style="margin-bottom: 0;">
                <label>{{ $t('modals.createBook.existingAuthorLabel') }}</label>
                <select v-model="editBook.author_id" :required="editBookAuthorMode === 'existing'" @change="$emit('clearError')">
                  <option value="">{{ $t('modals.createBook.selectAuthorPlaceholder') }}</option>
                  <option v-for="author in authors" :key="author.id" :value="author.id">{{ author.name }}</option>
                </select>
              </div>
            </div>
            <div v-else>
              <div class="form-group">
                <label>{{ $t('modals.createBook.newAuthorNameLabel') }}</label>
                <input type="text" v-model="newAuthor.name" @input="$emit('clearError')">
              </div>
              <div class="form-group">
                <label>{{ $t('modals.createBook.newAuthorBioLabel') }}</label>
                <textarea v-model="newAuthor.bio" rows="3"></textarea>
              </div>
              <div class="form-group">
                <label>{{ $t('modals.createBook.newAuthorPhotoLabel') }}</label>
                <input type="text" v-model="newAuthor.photo" :placeholder="$t('modals.createBook.authorPhotoPlaceholder')">
                <div class="mt-3">
                  <button type="button" class="btn btn-small" @click="$emit('upload', 'author')">{{ $t('common.upload') }}</button>
                </div>
              </div>
            </div>
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
    newAuthor: { type: Object, required: true },
    editBookAuthorMode: { type: String, required: true },
    editBookError: { type: String, required: true },
  },
};
</script>
