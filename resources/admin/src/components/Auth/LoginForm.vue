<template>
  <div>
    <h2>Entrar na sua conta</h2>
    <form @submit.prevent="$emit('submit')">
      <div class="form-group">
        <label>Email:</label>
        <input
          type="email"
          :value="loginEmail"
          @input="$emit('update:loginEmail', $event.target.value)"
          required
        >
      </div>
      <div class="form-group">
        <label>Senha:</label>
        <div style="display:flex; gap:8px; align-items:center;">
          <input
            :type="loginPasswordVisible ? 'text' : 'password'"
            :value="loginPassword"
            @input="$emit('update:loginPassword', $event.target.value)"
            required
            style="flex: 1;"
          >
          <button
            type="button"
            class="btn btn-small btn-secondary"
            style="white-space: nowrap;"
            @click="$emit('update:loginPasswordVisible', !loginPasswordVisible)"
          >
            {{ loginPasswordVisible ? 'Ocultar' : 'Mostrar' }}
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 4px;">
        Entrar
      </button>
    </form>

    <p class="text-center mt-2">
      <a href="#" @click.prevent="$emit('openReset')">Esqueceu a senha?</a>
    </p>
    <p class="text-center mt-3">
      Não tem conta?
      <a href="#" @click.prevent="$emit('showRegister')">Criar conta</a>
    </p>
    <button
      class="btn btn-secondary"
      style="margin-top: 10px; width: 100%;"
      @click="$emit('goToLanding')"
    >
      ← Voltar para Home
    </button>
  </div>
</template>

<script>
export default {
  name: 'LoginForm',
  props: {
    loginEmail: { type: String, required: true },
    loginPassword: { type: String, required: true },
    loginPasswordVisible: { type: Boolean, required: true },
  },
};
</script>
