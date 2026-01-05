<template>
  <div class="login-form">
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="email">Email</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          required
          :disabled="loading"
        />
      </div>
      
      <div class="form-group">
        <label for="password">Senha</label>
        <div class="password-input">
          <input
            id="password"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            required
            :disabled="loading"
          />
          <button
            type="button"
            class="toggle-password"
            @click="showPassword = !showPassword"
          >
            {{ showPassword ? '👁️' : '👁️‍🗨️' }}
          </button>
        </div>
      </div>

      <div v-if="error" class="error-message">
        {{ error }}
      </div>

      <button type="submit" class="btn btn-primary" :disabled="loading">
        <LoadingSpinner v-if="loading" text="Entrando..." />
        <span v-else>Entrar</span>
      </button>
    </form>
  </div>
</template>

<script>
import LoadingSpinner from '../common/LoadingSpinner.vue'

export default {
  name: 'LoginForm',
  components: { LoadingSpinner },
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      showPassword: false,
      loading: false,
      error: ''
    }
  },
  methods: {
    async handleSubmit() {
      this.loading = true
      this.error = ''
      
      try {
        const response = await this.$axios.post('/api/login', this.form)
        localStorage.setItem('token', response.data.token)
        this.$emit('success', response.data.user)
      } catch (error) {
        this.error = error.response?.data?.message || 'Erro ao fazer login'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.login-form {
  max-width: 400px;
  margin: 0 auto;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.password-input {
  position: relative;
}

.password-input input {
  padding-right: 50px;
}

.toggle-password {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.2rem;
}

.error-message {
  color: #dc3545;
  margin-bottom: 1rem;
  padding: 0.75rem;
  background: #f8d7da;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  width: 100%;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-primary:disabled {
  background: #6c757d;
  cursor: not-allowed;
}
</style>
