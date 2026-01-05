<template>
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h2>👥 Usuários</h2>
    </div>
    <div v-if="usersLoading" class="text-center">Carregando usuários...</div>
    <div v-else>
      <div class="user-grid">
        <div v-for="u in paginatedUsers" :key="u.id" class="user-card" @click="$emit('open-user-profile', u)">
          <div class="user-card-cover">
            <img :src="thumb(u.photo || 'pedbook/profiles/default-user', 400, 400, 'user')" alt="avatar">
          </div>
          <div class="user-card-body">
            <div class="user-name">{{ u.name }}</div>
            <div class="user-email">{{ u.email }}</div>
            <div style="display:flex; justify-content: space-between; align-items:center; margin-top:4px;">
              <span class="user-role">{{ u.is_admin ? 'Administrador' : 'Usuário' }}</span>
              <span style="font-size:0.75rem; color:#888;">Clique para ver perfil completo</span>
            </div>
          </div>
        </div>
      </div>

      <div class="pagination" v-if="totalUsersPages > 1">
        <button class="page-link" @click="$emit('change-users-page', usersPage - 1)" :disabled="usersPage === 1">Anterior</button>
        <button v-for="page in totalUsersPages" :key="page" class="page-link" :class="{ active: page === usersPage }" @click="$emit('change-users-page', page)">{{ page }}</button>
        <button class="page-link" @click="$emit('change-users-page', usersPage + 1)" :disabled="usersPage === totalUsersPages">Próxima</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UsersPage',
  props: {
    usersLoading: { type: Boolean, required: true },
    paginatedUsers: { type: Array, required: true },
    totalUsersPages: { type: Number, required: true },
    usersPage: { type: Number, required: true },
    thumb: { type: Function, required: true },
  },
};
</script>
