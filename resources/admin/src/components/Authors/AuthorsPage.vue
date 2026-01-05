<template>
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h2>✍️ Autores</h2>
      <button
        v-if="currentUser && currentUser.is_admin"
        class="btn btn-small"
        @click="$emit('openCreateAuthor')"
      >
        ➕ Adicionar Autor
      </button>
    </div>

    <div style="margin-bottom: 12px; display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
      <input
        type="text"
        :value="authorsSearchQuery"
        @input="$emit('update:authorsSearchQuery', $event.target.value)"
        placeholder="Buscar autores..."
        style="flex:1; min-width: 240px; padding:10px; border:1px solid #dee2e6; border-radius:6px;"
      >
      <select
        :value="authorsPerPage"
        @change="$emit('update:authorsPerPage', Number($event.target.value))"
        style="padding:10px;border:1px solid #dee2e6;border-radius:6px; min-width:160px;"
      >
        <option :value="5">5 / página</option>
        <option :value="10">10 / página</option>
        <option :value="20">20 / página</option>
        <option :value="50">50 / página</option>
      </select>
    </div>

    <div v-if="authorsLoading" class="text-center">Carregando autores...</div>

    <div v-else>
      <div class="book-grid">
        <div
          v-for="author in paginatedAuthors"
          :key="author.id"
          class="book-card"
          style="cursor: pointer;"
          @click="$emit('select-author', author)"
        >
          <img
            :src="thumb(author.photo, 120, 120, 'author')"
            :alt="author.name"
            class="author-photo"
            referrerpolicy="no-referrer"
            crossorigin="anonymous"
            loading="lazy"
          >
          <div class="book-card-body">
            <h3 class="book-title">{{ author.name }}</h3>
            <p class="book-author">📚 {{ author.books && author.books.length ? author.books.length : 0 }} livros</p>
            <div v-if="currentUser && currentUser.is_admin" style="margin-top:10px; display:flex; gap:8px;">
              <button class="btn btn-small" @click.stop="$emit('openEditAuthor', author)">Editar</button>
              <button class="btn btn-small btn-danger" @click.stop="$emit('askDeleteAuthor', author.id)">Excluir</button>
            </div>
          </div>
        </div>
      </div>

      <div class="pagination" v-if="totalAuthorsPages > 1">
        <button class="page-link" @click="$emit('changeAuthorsPage', authorsPage - 1)" :disabled="authorsPage === 1">Anterior</button>
        <button v-for="page in totalAuthorsPages" :key="page" class="page-link" :class="{ active: page === authorsPage }" @click="$emit('changeAuthorsPage', page)">{{ page }}</button>
        <button class="page-link" @click="$emit('changeAuthorsPage', authorsPage + 1)" :disabled="authorsPage === totalAuthorsPages">Próxima</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AuthorsPage',
  props: {
    currentUser: { type: Object, default: null },
    authorsLoading: { type: Boolean, required: true },

    authorsSearchQuery: { type: String, required: true },
    authorsPerPage: { type: Number, required: true },

    paginatedAuthors: { type: Array, required: true },
    totalAuthorsPages: { type: Number, required: true },
    authorsPage: { type: Number, required: true },

    thumb: { type: Function, required: true },
  },
};
</script>
