<template>
  <div class="container">
    <button
      class="btn btn-secondary btn-small"
      style="width: auto; margin-bottom: 15px;"
      @click="$emit('goBack')"
    >
      ← Voltar
    </button>

    <div class="detail-container" style="display: grid; grid-template-columns: 300px 1fr; gap: 30px;">
      <div class="detail-image" style="width: 300px; height: 400px; border-radius: 8px; overflow: hidden;">
        <img
          :src="thumb(selectedBook.photo, 600, 800, 'book')"
          :alt="selectedBook.title"
          @error="$event.target.src = thumb('', 600, 800, 'book')"
          style="width:100%;height:100%;object-fit:cover;"
        >
      </div>
      <div class="detail-info">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin:0 0 10px 0;">
          <h2 style="margin:0;">{{ selectedBook.title }}</h2>
          <button
            v-if="currentUser && currentUser.is_admin"
            class="btn btn-small"
            style="width:auto;"
            @click="$emit('openEditBook', selectedBook)"
          >
            Editar
          </button>
        </div>

        <p style="color:#666; margin: 5px 0 15px 0;">
          Autor:
          <strong
            style="cursor:pointer;color:#162c74;text-decoration:underline;"
            @click="$emit('select-author', selectedBook.author)"
          >
            {{ selectedBook.author && selectedBook.author.name ? selectedBook.author.name : 'Autor Desconhecido' }}
          </strong>
        </p>

        <p style="color:#666; line-height:1.6;">{{ selectedBook.description || 'Sem descrição disponível.' }}</p>

        <p style="margin-top:10px; font-weight:700; color:#162c74;">
          {{ 'R$ ' + getBookPrice(selectedBook).toFixed(2).replace('.', ',') }}
        </p>

        <div style="margin-top:16px; background:#f8f9fa; padding:12px; border-radius:8px;">
          <h3 style="margin:0 0 10px 0;">Alugar</h3>

          <div v-if="currentUser && !currentUser.is_admin">
            <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
              <div v-if="isBookBorrowedByMe(selectedBook.id) && selectedBook.return_date" class="form-group" style="margin:0;">
                <label>Devolução</label>
                <div style="height: 38px; display:flex; align-items:center; padding:0 10px; border:1px solid #dee2e6; border-radius:6px; background:#fff; min-width: 140px;">
                  {{ selectedBook.return_date }}
                </div>
              </div>

              <button
                v-if="!isBookBorrowedByMe(selectedBook.id)"
                class="btn btn-small"
                style="width:auto;"
                :disabled="isBookUnavailable(selectedBook.id)"
                @click="$emit('borrow')"
              >
                {{ isBookUnavailable(selectedBook.id) ? 'Indisponível' : 'Alugar' }}
              </button>

              <button
                v-else
                class="btn btn-small btn-danger"
                style="width:auto;"
                @click="$emit('promptReturn', selectedBook.id)"
              >
                Devolver
              </button>

              <button class="btn btn-small" style="width:auto;" @click.stop="$emit('toggleFavorite')">
                {{ isFavorite(selectedBook.id) ? 'Remover dos favoritos' : 'Adicionar aos favoritos' }}
              </button>
              <button class="btn btn-small btn-secondary" style="width:auto;" @click.stop="$emit('addToCart')">
                🛒 Adicionar ao carrinho
              </button>
            </div>

            <div v-if="isBookBorrowedByMe(selectedBook.id)" style="margin-top:8px; font-size:0.85rem; color:#16a34a;">
              ✅ Você está com este livro emprestado
            </div>
            <div v-else-if="isBookUnavailable(selectedBook.id)" style="margin-top:8px; font-size:0.85rem; color:#b91c1c;">
              ⚠️ Livro alugado por outro usuário
            </div>
          </div>

          <div v-else>
            <button v-if="!currentUser" class="btn btn-small" @click="$emit('goLogin')">🔐 Faça login para alugar ou comprar</button>
            <div v-else style="padding:8px 0; color:#555; font-size:0.9rem;">👨‍💼 Administradores não podem alugar ou favoritar livros</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BookDetailPage',
  props: {
    selectedBook: { type: Object, required: true },
    currentUser: { type: Object, default: null },

    thumb: { type: Function, required: true },
    getBookPrice: { type: Function, required: true },
    isBookBorrowedByMe: { type: Function, required: true },
    isBookUnavailable: { type: Function, required: true },
    isFavorite: { type: Function, required: true },
  },
};
</script>
