<template>
  <div class="container">
    <button
      class="btn btn-secondary btn-small"
      style="width:auto; margin-bottom: 15px;"
      @click="$emit('goBack')"
    >
      ← Voltar
    </button>

    <div class="detail-container" style="display:grid; grid-template-columns: 300px 1fr; gap: 30px;">
      <div style="width: 300px; height: 300px; border-radius: 8px; overflow: hidden;">
        <img
          :src="thumb((selectedAuthor.photo || 'pedbook/profiles/default-user'), 600, 600)"
          :alt="selectedAuthor.name"
          style="width:100%; height:100%; object-fit:cover;"
        >
      </div>
      <div>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
          <h2 style="margin:0;">{{ selectedAuthor.name }}</h2>
          <button
            v-if="currentUser && currentUser.is_admin"
            class="btn btn-small"
            style="width:auto;"
            @click="$emit('openEditAuthor', selectedAuthor)"
          >
            Editar
          </button>
        </div>

        <p style="color:#666; line-height:1.6; margin-top:10px;">{{ selectedAuthor.bio || 'Sem biografia.' }}</p>

        <h3 style="margin:20px 0 10px 0;">Livros</h3>
        <div class="book-grid">
          <div
            v-for="b in (selectedAuthor.books || [])"
            :key="b.id"
            class="book-card"
            :class="{ borrowed: isBookUnavailable(b.id) }"
            style="cursor:pointer;"
            @click="$emit('viewBook', b)"
          >
            <div v-if="isBookUnavailable(b.id)" class="banner-rented">✅ ALUGADO</div>
            <img
              :src="thumb(b.photo, 600, 360, 'book')"
              :alt="b.title"
              @error="$event.target.src = thumb('', 600, 360, 'book')"
            >
            <div class="book-card-body">
              <div class="book-title">{{ b.title }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AuthorDetailPage',
  props: {
    selectedAuthor: { type: Object, required: true },
    currentUser: { type: Object, default: null },
    thumb: { type: Function, required: true },
    isBookUnavailable: { type: Function, required: true },
  },
};
</script>
