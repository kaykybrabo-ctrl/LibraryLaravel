<template>
  <div class="modal" :class="{ active: show }" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3>{{ $t('modals.createBook.title') }}</h3>
        <button class="modal-close" @click="$emit('close')">&times;</button>
      </div>
      <div class="modal-body">
        <div v-if="newBookError" class="error" style="margin-bottom:10px;">{{ newBookError }}</div>
        <form @submit.prevent="$emit('submit')" class="modal-form">
          <div class="form-group">
            <label>{{ $t('books.title') }}:</label>
            <input type="text" v-model="newBook.title" required>
          </div>

          <div class="modal-section">
            <div class="modal-section-title">{{ $t('modals.createBook.authorSectionTitle') }}</div>
            <div class="modal-section-helper">{{ $t('modals.createBook.authorSectionHelper') }}</div>
            <div style="margin-bottom: 10px; font-size: 0.9rem; display:flex; flex-wrap:wrap; gap:12px;">
              <label>
                <input
                  type="radio"
                  value="existing"
                  :checked="newBookAuthorMode === 'existing'"
                  @change="$emit('update:newBookAuthorMode', 'existing'); $emit('clearError')"
                >
                {{ $t('modals.createBook.selectExistingAuthor') }}
              </label>
              <label>
                <input
                  type="radio"
                  value="new"
                  :checked="newBookAuthorMode === 'new'"
                  @change="$emit('update:newBookAuthorMode', 'new'); $emit('clearError')"
                >
                {{ $t('modals.createBook.createNewAuthorWithBook') }}
              </label>
            </div>
            <div v-if="newBookAuthorMode === 'existing'">
              <div class="form-group" style="margin-bottom: 0;">
                <label>{{ $t('modals.createBook.existingAuthorLabel') }}</label>
                <select v-model="newBook.author_id" @change="$emit('clearError')">
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

          <div class="modal-two-columns">
            <div class="form-group">
              <label>{{ $t('books.description') }}:</label>
              <textarea v-model="newBook.description" rows="4" required></textarea>
            </div>
            <div class="form-group">
              <label>{{ $t('modals.createBook.bookPhotoLabel') }}</label>
              <input type="text" v-model="newBook.photo" :placeholder="$t('modals.createBook.bookPhotoPlaceholder')">
              <div class="mt-3">
                <button type="button" class="btn btn-small" @click="$emit('upload', 'book')">{{ $t('common.upload') }}</button>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>{{ $t('modals.createBook.priceLabel') }}</label>
            <input type="number" step="0.01" min="0" v-model.number="newBook.price">
          </div>

          <button type="submit" class="btn btn-primary">{{ $t('common.save') }}</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CreateBookModal',
  props: {
    show: { type: Boolean, required: true },
    authors: { type: Array, required: true },
    newBook: { type: Object, required: true },
    newAuthor: { type: Object, required: true },
    newBookError: { type: String, required: true },
    newBookAuthorMode: { type: String, required: true },
  },
};
</script>
