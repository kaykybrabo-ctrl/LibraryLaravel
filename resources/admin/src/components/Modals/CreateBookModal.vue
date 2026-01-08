<template>
  <div class="modal" :class="{ active: show }" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3>{{ $t('modals.createBook.title') }}</h3>
        <button class="modal-close" @click="$emit('close')">&times;</button>
      </div>
      <div class="modal-body">
        <div v-if="newBookError" class="error" style="margin-bottom:10px;">{{ newBookError }}</div>
        <form @submit.prevent="handleSubmit" class="modal-form">
          <div class="form-group">
            <label>{{ $t('books.title') }}:</label>
            <input type="text" v-model="newBook.title" required>
            <div
              v-if="localErrors.title"
              style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
            >
              {{ localErrors.title }}
            </div>
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
                <div
                  v-if="localErrors.author"
                  style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
                >
                  {{ localErrors.author }}
                </div>
              </div>
            </div>
            <div v-else>
              <div class="form-group">
                <label>{{ $t('modals.createBook.newAuthorNameLabel') }}</label>
                <input type="text" v-model="newAuthor.name" @input="$emit('clearError')">
                <div
                  v-if="localErrors.author"
                  style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
                >
                  {{ localErrors.author }}
                </div>
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
              <div
                v-if="localErrors.description"
                style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
              >
                {{ localErrors.description }}
              </div>
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
            <div
              v-if="localErrors.price"
              style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
            >
              {{ localErrors.price }}
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
  name: 'CreateBookModal',
  props: {
    show: { type: Boolean, required: true },
    authors: { type: Array, required: true },
    newBook: { type: Object, required: true },
    newAuthor: { type: Object, required: true },
    newBookError: { type: String, required: true },
    newBookAuthorMode: { type: String, required: true },
  },
  data() {
    return {
      localErrors: {
        title: '',
        description: '',
        author: '',
        price: '',
      },
    };
  },
  methods: {
    handleSubmit() {
      this.localErrors.title = '';
      this.localErrors.description = '';
      this.localErrors.author = '';
      this.localErrors.price = '';

      const title = (this.newBook.title || '').trim();
      const description = (this.newBook.description || '').trim();
      const authorMode = this.newBookAuthorMode;
      const authorId = this.newBook.author_id;
      const newAuthorName = this.newAuthor && this.newAuthor.name
        ? this.newAuthor.name.trim()
        : '';
      const price = this.newBook.price;

      const requiredMsg = this.$t
        ? (this.$t('validation.required') || 'Campo obrigatório')
        : 'Campo obrigatório';

      if (!title) {
        this.localErrors.title = requiredMsg;
      }

      if (!description) {
        this.localErrors.description = requiredMsg;
      }

      if (authorMode === 'existing') {
        if (!authorId) {
          this.localErrors.author = requiredMsg;
        }
      } else if (authorMode === 'new') {
        if (!newAuthorName) {
          this.localErrors.author = requiredMsg;
        }
      }

      if (price !== null && price !== undefined && price !== '') {
        const numeric = Number(price);
        if (Number.isNaN(numeric)) {
          this.localErrors.price = this.$t
            ? (this.$t('validation.numeric') || 'Valor inválido')
            : 'Valor inválido';
        } else if (numeric < 0) {
          this.localErrors.price = this.$t
            ? (this.$t('validation.min.numeric') || 'Valor não pode ser negativo')
            : 'Valor não pode ser negativo';
        }
      }

      if (
        this.localErrors.title
        || this.localErrors.description
        || this.localErrors.author
        || this.localErrors.price
      ) {
        return;
      }

      this.$emit('submit');
    },
  },
};
</script>
