<template>
  <div class="modal" :class="{ active: show }" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3>{{ $t('modals.createAuthor.title') }}</h3>
        <button class="modal-close" @click="$emit('close')">&times;</button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="handleSubmit" novalidate>
          <div class="form-group">
            <label>{{ $t('auth.name') }}:</label>
            <input type="text" v-model="newAuthor.name" required>
            <div
              v-if="localErrors.name"
              style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
            >
              {{ localErrors.name }}
            </div>
          </div>
          <div class="form-group">
            <label>{{ $t('modals.createAuthor.bioLabel') }}</label>
            <textarea v-model="newAuthor.bio" rows="4" required></textarea>
            <div
              v-if="localErrors.bio"
              style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
            >
              {{ localErrors.bio }}
            </div>
          </div>
          <div class="form-group">
            <label>{{ $t('modals.createAuthor.photoLabel') }}</label>
            <input type="text" v-model="newAuthor.photo" :placeholder="$t('modals.createAuthor.photoPlaceholder')">
            <div class="mt-3">
              <button type="button" class="btn btn-small" @click="$emit('upload', 'author')">{{ $t('common.upload') }}</button>
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
  name: 'CreateAuthorModal',
  props: {
    show: { type: Boolean, required: true },
    newAuthor: { type: Object, required: true },
  },
  data() {
    return {
      localErrors: {
        name: '',
        bio: '',
      },
    };
  },
  methods: {
    handleSubmit() {
      this.localErrors.name = '';
      this.localErrors.bio = '';

      const name = this.newAuthor && this.newAuthor.name
        ? this.newAuthor.name.trim()
        : '';
      const bio = this.newAuthor && this.newAuthor.bio
        ? this.newAuthor.bio.trim()
        : '';

      const requiredMsg = this.$t
        ? (this.$t('validation.required') || 'Campo obrigatório')
        : 'Campo obrigatório';

      if (!name) {
        this.localErrors.name = requiredMsg;
      }

      if (!bio) {
        this.localErrors.bio = requiredMsg;
      }

      if (this.localErrors.name || this.localErrors.bio) {
        if (typeof window !== 'undefined' && window.$uiStore) {
          const msg = this.$t
            ? this.$t('errors.validationFailed')
            : 'Existem erros no formulário. Verifique os campos.';
          window.$uiStore.showToast('error', msg);
        }
        return;
      }

      this.$emit('submit');
    },
  },
};
</script>
