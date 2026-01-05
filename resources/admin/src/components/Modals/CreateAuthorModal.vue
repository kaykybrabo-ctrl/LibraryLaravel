<template>
  <div class="modal" :class="{ active: show }" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Adicionar Novo Autor</h3>
        <button class="modal-close" @click="$emit('close')">✕</button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="$emit('submit')">
          <div class="form-group">
            <label>Nome:</label>
            <input type="text" v-model="newAuthor.name" required>
          </div>
          <div class="form-group">
            <label>Biografia:</label>
            <textarea v-model="newAuthor.bio" rows="4" required></textarea>
          </div>
          <div class="form-group">
            <label>Foto (URL ou Cloudinary publicId):</label>
            <input type="text" v-model="newAuthor.photo" placeholder="ex.: pedbook/profiles/author-nome">
            <div class="mt-3">
              <button type="button" class="btn btn-small" @click="$emit('upload', 'author')">Upload</button>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Salvar</button>
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
