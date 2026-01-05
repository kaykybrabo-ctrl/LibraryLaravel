<template>
  <div class="modal" :class="{ active: show }" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3 style="color:#162c74; font-weight:600;">Editar Livro</h3>
        <button class="modal-close" @click="$emit('close')">✕</button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="$emit('submit')">
          <div class="form-group">
            <label>Título:</label>
            <input type="text" v-model="editBook.title" required>
          </div>
          <div class="form-group">
            <label>Autor:</label>
            <select v-model="editBook.author_id" required>
              <option v-for="author in authors" :key="author.id" :value="author.id">{{ author.name }}</option>
            </select>
          </div>
          <div class="form-group">
            <label>Descrição:</label>
            <textarea v-model="editBook.description" rows="4" required></textarea>
          </div>
          <div class="form-group">
            <label>Preço (R$):</label>
            <input type="number" step="0.01" min="0" v-model.number="editBook.price">
          </div>
          <div class="form-group">
            <label>Foto (URL ou Cloudinary publicId):</label>
            <input type="text" v-model="editBook.photo" required>
            <div class="mt-3">
              <button type="button" class="btn btn-small" @click="$emit('upload', 'book_edit')">Upload</button>
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
  name: 'EditBookModal',
  props: {
    show: { type: Boolean, required: true },
    authors: { type: Array, required: true },
    editBook: { type: Object, required: true },
  },
};
</script>
