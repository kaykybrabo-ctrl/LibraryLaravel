<template>
  <div class="modal" :class="{ active: show }" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Adicionar Novo Livro</h3>
        <button class="modal-close" @click="$emit('close')">✕</button>
      </div>
      <div class="modal-body">
        <div v-if="newBookError" class="error" style="margin-bottom:10px;">{{ newBookError }}</div>
        <form @submit.prevent="$emit('submit')" class="modal-form">
          <div class="form-group">
            <label>Título:</label>
            <input type="text" v-model="newBook.title" required>
          </div>

          <div class="modal-section">
            <div class="modal-section-title">Autor do livro</div>
            <div class="modal-section-helper">Escolha um autor existente ou cadastre um novo para este livro.</div>
            <div style="margin-bottom: 10px; font-size: 0.9rem; display:flex; flex-wrap:wrap; gap:12px;">
              <label>
                <input
                  type="radio"
                  value="existing"
                  :checked="newBookAuthorMode === 'existing'"
                  @change="$emit('update:newBookAuthorMode', 'existing'); $emit('clearError')"
                >
                Selecionar autor existente
              </label>
              <label>
                <input
                  type="radio"
                  value="new"
                  :checked="newBookAuthorMode === 'new'"
                  @change="$emit('update:newBookAuthorMode', 'new'); $emit('clearError')"
                >
                Criar novo autor junto com o livro
              </label>
            </div>
            <div v-if="newBookAuthorMode === 'existing'">
              <div class="form-group" style="margin-bottom: 0;">
                <label>Autor existente:</label>
                <select v-model="newBook.author_id" @change="$emit('clearError')">
                  <option value="">Selecione um autor</option>
                  <option v-for="author in authors" :key="author.id" :value="author.id">{{ author.name }}</option>
                </select>
              </div>
            </div>
            <div v-else>
              <div class="form-group">
                <label>Nome do autor:</label>
                <input type="text" v-model="newAuthor.name" @input="$emit('clearError')">
              </div>
              <div class="form-group">
                <label>Biografia do autor:</label>
                <textarea v-model="newAuthor.bio" rows="3"></textarea>
              </div>
              <div class="form-group">
                <label>Foto do autor (URL ou Cloudinary publicId):</label>
                <input type="text" v-model="newAuthor.photo" placeholder="ex.: pedbook/profiles/author-nome">
                <div class="mt-3">
                  <button type="button" class="btn btn-small" @click="$emit('upload', 'author')">Upload</button>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-two-columns">
            <div class="form-group">
              <label>Descrição:</label>
              <textarea v-model="newBook.description" rows="4" required></textarea>
            </div>
            <div class="form-group">
              <label>Foto (URL ou Cloudinary publicId):</label>
              <input type="text" v-model="newBook.photo" placeholder="ex.: pedbook/books/book-life-in-silence">
              <div class="mt-3">
                <button type="button" class="btn btn-small" @click="$emit('upload', 'book')">Upload</button>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Preço (R$):</label>
            <input type="number" step="0.01" min="0" v-model.number="newBook.price">
          </div>

          <button type="submit" class="btn btn-primary">Salvar</button>
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
