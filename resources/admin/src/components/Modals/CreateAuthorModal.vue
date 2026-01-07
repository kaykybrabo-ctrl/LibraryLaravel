<template>
  <div class="modal" :class="{ active: show }" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3>{{ $t('modals.createAuthor.title') }}</h3>
        <button class="modal-close" @click="$emit('close')">&times;</button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="$emit('submit')">
          <div class="form-group">
            <label>{{ $t('auth.name') }}:</label>
            <input type="text" v-model="newAuthor.name" required>
          </div>
          <div class="form-group">
            <label>{{ $t('modals.createAuthor.bioLabel') }}</label>
            <textarea v-model="newAuthor.bio" rows="4" required></textarea>
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
};
</script>
