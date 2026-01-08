<template>
  <div class="modal" :class="{ active: show }" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3 style="color:#162c74; font-weight:600;">{{ $t('modals.editAuthor.title') }}</h3>
        <button class="modal-close" @click="$emit('close')">&times;</button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="handleSubmit">
          <div class="form-group">
            <label>{{ $t('auth.name') }}:</label>
            <input type="text" v-model="editAuthor.name" required>
            <div
              v-if="localErrors.name"
              style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
            >
              {{ localErrors.name }}
            </div>
          </div>
          <div class="form-group">
            <label>{{ $t('modals.editAuthor.bioLabel') }}</label>
            <textarea v-model="editAuthor.bio" rows="4" required></textarea>
            <div
              v-if="localErrors.bio"
              style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
            >
              {{ localErrors.bio }}
            </div>
          </div>
          <div class="form-group">
            <label>{{ $t('modals.editAuthor.photoLabel') }}</label>
            <input type="text" v-model="editAuthor.photo">
            <div class="mt-3">
              <button type="button" class="btn btn-small" @click="$emit('upload', 'author_edit')">{{ $t('common.upload') }}</button>
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
  name: 'EditAuthorModal',
  props: {
    show: { type: Boolean, required: true },
    editAuthor: { type: Object, required: true },
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

      const name = this.editAuthor && this.editAuthor.name
        ? this.editAuthor.name.trim()
        : '';
      const bio = this.editAuthor && this.editAuthor.bio
        ? this.editAuthor.bio.trim()
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
        return;
      }

      this.$emit('submit');
    },
  },
};
</script>
