<template>
  <div class="container">
    <div style="display:flex; justify-content: space-between; align-items:center; margin-bottom: 12px;">
      <h2>👤 Perfil do Usuário</h2>
      <button class="btn btn-small btn-secondary" @click="$emit('goBack')" style="width:auto;">← Voltar para Usuários</button>
    </div>

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
        <div style="width: 100px; height: 100px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; overflow: hidden;">
          <img v-if="selectedUser.photo" :src="thumb(selectedUser.photo, 100, 100)" alt="avatar" style="width:100%; height:100%; object-fit: cover;">
          <span v-else>👤</span>
        </div>
        <div>
          <h3 style="margin: 0 0 5px 0; color: #162c74;">{{ selectedUser.name }}</h3>
          <p style="margin: 0; color: #666;">{{ selectedUser.email }}</p>
          <span style="display: inline-block; margin-top: 5px; padding: 3px 8px; background: #e9ecef; border-radius: 12px; font-size: 0.8rem; color: #495057;">
            {{ selectedUser.is_admin ? 'Administrador' : 'Usuário' }}
          </span>
          <p style="margin: 8px 0 0 0; color:#777; font-size:0.85rem;">
            {{ selectedUser.is_admin ? 'Conta de administrador do sistema.' : 'Conta de leitor da biblioteca.' }}
          </p>
        </div>
      </div>

      <div v-if="selectedUserActiveLoans.length > 0">
        <h3 style="margin: 20px 0 10px 0; color: #162c74;">Empréstimos desse usuário</h3>
        <div v-for="loan in selectedUserActiveLoans" :key="loan.id" style="padding: 15px; border-bottom: 1px solid #eee;">
          <h4 style="margin: 0 0 5px 0; color: #162c74;">{{ loan.book && loan.book.title ? loan.book.title : '' }}</h4>
          <p style="margin: 0; color: #666; font-size: 0.9rem;">Emprestado em: {{ formatDate(loan.loan_date) }}</p>
          <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9rem;">Data de devolução prevista: {{ formatDate(loan.return_date) }}</p>
        </div>
      </div>

      <div style="margin-top: 30px;">
        <h3 style="margin: 0 0 10px 0; color: #162c74;">Livro favorito desse usuário</h3>
        <div v-if="selectedUserFavoriteBook" style="background: #f8f9fa; padding: 15px; border-radius: 8px; display:flex; gap:16px; align-items:center;">
          <div style="width:72px; height:96px; border-radius:6px; overflow:hidden; background:#f3f4f6; flex-shrink:0;">
            <img
              v-if="selectedUserFavoriteBook.photo"
              :src="thumb(selectedUserFavoriteBook.photo, 300, 420, 'book')"
              :alt="selectedUserFavoriteBook.title"
              @error="$event.target.src = thumb('', 300, 420, 'book')"
              referrerpolicy="no-referrer"
              crossorigin="anonymous"
              style="width:100%; height:100%; object-fit:cover;"
            >
            <div v-else style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#9ca3af; font-size:2rem;">
              📚
            </div>
          </div>
          <div style="flex:1;">
            <h4 style="margin: 0 0 5px 0; color: #162c74;">{{ selectedUserFavoriteBook.title }}</h4>
            <p style="margin: 0; color: #666; font-size: 0.9rem;">por
              <span v-if="selectedUserFavoriteBook.author" @click="$emit('selectAuthor', selectedUserFavoriteBook.author)" style="cursor:pointer;color:#162c74;text-decoration:underline;">
                {{ selectedUserFavoriteBook.author && selectedUserFavoriteBook.author.name ? selectedUserFavoriteBook.author.name : '' }}
              </span>
              <span v-else>Autor Desconhecido</span>
            </p>
          </div>
        </div>
        <p v-else style="color: #666;">Este usuário ainda não tem um livro favorito.</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserDetailPage',
  props: {
    selectedUser: { type: Object, required: true },
    selectedUserActiveLoans: { type: Array, required: true },
    selectedUserFavoriteBook: { type: Object, default: null },
    thumb: { type: Function, required: true },
    formatDate: { type: Function, required: true },
  },
};
</script>
