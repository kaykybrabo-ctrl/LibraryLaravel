<template>
  <nav class="navbar">
    <div class="nav-brand">
      <router-link to="/" class="brand-link">
        <h1>PedBook</h1>
      </router-link>
    </div>
    
    <div class="nav-menu" :class="{ active: menuOpen }">
      <router-link to="/books" class="nav-link" :class="{ active: currentView === 'books' }">
        Livros
      </router-link>
      <router-link to="/authors" class="nav-link" :class="{ active: currentView === 'authors' }">
        Autores
      </router-link>
      <router-link v-if="isLoggedIn" to="/loans" class="nav-link" :class="{ active: currentView === 'loans' }">
        Meus Empréstimos
      </router-link>
      
      <div class="nav-actions">
        <template v-if="!isLoggedIn">
          <button @click="$emit('showLogin')" class="btn btn-outline">
            Entrar
          </button>
          <button @click="$emit('showRegister')" class="btn btn-outline">
            Cadastrar
          </button>
        </template>
        
        <template v-else>
          <span class="user-welcome">Bem-vindo, {{ user.name }}</span>
          <button v-if="isAdmin" @click="$emit('setView', 'admin')" class="btn btn-outline">
            Admin
          </button>
          <button @click="$emit('logout')" class="btn btn-outline">
            Sair
          </button>
        </template>
      </div>
      
      <button class="nav-toggle" @click="toggleMenu">
        ☰
      </button>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'Navigation',
  props: {
    user: Object,
    currentView: String,
    isAdmin: Boolean
  },
  data() {
    return {
      menuOpen: false
    }
  },
  methods: {
    toggleMenu() {
      this.menuOpen = !this.menuOpen;
    },
    closeMenu() {
      this.menuOpen = false;
    }
  },
  mounted() {
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.navbar')) {
        this.closeMenu();
      }
    });
  }
}
</script>

<style scoped>
.navbar {
  background: #fff;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  padding: 1rem 2rem;
  position: sticky;
  top: 0;
  z-index: 1000;
}

.nav-brand {
  flex: 1;
}

.brand-link {
  text-decoration: none;
  color: #333;
}

.brand-link h1 {
  margin: 0;
  font-size: 1.5rem;
  color: #007bff;
}

.nav-menu {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.nav-link {
  text-decoration: none;
  color: #666;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  transition: all 0.3s;
}

.nav-link:hover,
.nav-link.active {
  background: #007bff;
  color: white;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.user-welcome {
  color: #666;
  font-size: 0.9rem;
}

.nav-toggle {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  display: none;
}

.btn {
  padding: 0.5rem 1rem;
  border: 1px solid #007bff;
  border-radius: 4px;
  background: white;
  color: #007bff;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.3s;
}

.btn:hover {
  background: #007bff;
  color: white;
}

.btn-outline {
  background: transparent;
}

@media (max-width: 768px) {
  .nav-menu {
    position: fixed;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    flex-direction: column;
    padding: 2rem;
    transform: translateX(100%);
    transition: transform 0.3s;
  }
  
  .nav-menu.active {
    transform: translateX(0);
  }
  
  .nav-toggle {
    display: block;
  }
}
</style>
