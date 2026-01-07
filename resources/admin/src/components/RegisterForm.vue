<template>
  <div class="register-form">
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="name">{{ $t('auth.name') }}</label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          required
          :disabled="loading"
        />
      </div>
      
      <div class="form-group">
        <label for="email">{{ $t('auth.email') }}</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          required
          :disabled="loading"
        />
      </div>
      
      <div class="form-group">
        <label for="password">{{ $t('auth.password') }}</label>
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
            {{ showPassword ? $t('auth.hidePassword') : $t('auth.showPassword') }}
          </button>
        </div>
      </div>
      
      <div class="form-group">
        <label for="password_confirmation">{{ $t('auth.confirmPassword') }}</label>
        <input
          id="password_confirmation"
          v-model="form.password_confirmation"
          :type="showPassword ? 'text' : 'password'"
          required
          :disabled="loading"
        />
      </div>

      <div v-if="error" class="error-message">
        {{ error }}
      </div>

      <button type="submit" class="btn btn-primary" :disabled="loading">
        <LoadingSpinner v-if="loading" :text="$t('auth.registering')" />
        <span v-else>{{ $t('auth.register') }}</span>
      </button>
    </form>
  </div>
</template>

<script>
import LoadingSpinner from './LoadingSpinner.vue';

export default {
  name: 'RegisterForm',
  components: { LoadingSpinner },
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      },
      showPassword: false,
      loading: false,
      error: ''
    }
  },
  methods: {
    async handleSubmit() {
      this.loading = true;
      this.error = '';
      
      try {
        const query = `
          mutation Register($input: RegisterInput!) {
            register(input: $input) {
              user {
                id
                name
                email
              }
              message
            }
          }
        `;
        
        const response = await this.$axios.post('/graphql', {
          query: query,
          variables: { input: this.form }
        });
        
        if (response.data.data.register) {
          this.$emit('success', response.data.data.register.user);
          this.form = { name: '', email: '', password: '', password_confirmation: '' };
        } else {
          this.error = response.data.errors[0].message || this.$t('errors.registerFailed');
        }
      } catch (error) {
        this.error = this.$t('errors.networkError');
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.register-form {
  max-width: 400px;
  margin: 0 auto;
  padding: 2rem;
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
