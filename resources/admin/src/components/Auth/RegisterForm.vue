<template>
  <div>
    <h2>Criar Conta</h2>
    <form @submit.prevent="$emit('submit')">
      <div class="form-group">
        <label>Nome:</label>
        <input
          type="text"
          :value="registerName"
          @input="$emit('update:registerName', $event.target.value)"
          required
        >
      </div>
      <div class="form-group">
        <label>Email:</label>
        <input
          type="email"
          :value="registerEmail"
          @input="$emit('update:registerEmail', $event.target.value)"
          required
        >
      </div>
      <div class="form-group">
        <label>Senha:</label>
        <div style="display:flex; gap:8px; align-items:center;">
          <input
            :type="registerPasswordVisible ? 'text' : 'password'"
            :value="registerPassword"
            @input="$emit('update:registerPassword', $event.target.value)"
            required
            style="flex: 1;"
          >
          <button
            type="button"
            class="btn btn-small btn-secondary"
            style="white-space: nowrap;"
            @click="$emit('update:registerPasswordVisible', !registerPasswordVisible)"
          >
            {{ registerPasswordVisible ? 'Ocultar' : 'Mostrar' }}
          </button>
        </div>
      </div>
      <div class="form-group">
        <label>Confirmar Senha:</label>
        <div style="display:flex; gap:8px; align-items:center;">
          <input
            :type="registerPasswordVisible ? 'text' : 'password'"
            :value="registerPasswordConfirmation"
            @input="$emit('update:registerPasswordConfirmation', $event.target.value)"
            required
            style="flex: 1;"
          >
          <button
            type="button"
            class="btn btn-small btn-secondary"
            style="white-space: nowrap;"
            @click="$emit('update:registerPasswordVisible', !registerPasswordVisible)"
          >
            {{ registerPasswordVisible ? 'Ocultar' : 'Mostrar' }}
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 4px;">
        Criar Conta
      </button>
    </form>

    <p class="text-center mt-3">
      Já tem conta?
      <a href="#" @click.prevent="$emit('showLogin')">Entrar</a>
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
  name: 'RegisterForm',
  props: {
    registerName: { type: String, required: true },
    registerEmail: { type: String, required: true },
    registerPassword: { type: String, required: true },
    registerPasswordConfirmation: { type: String, required: true },
    registerPasswordVisible: { type: Boolean, required: true },
  },
};
</script>
