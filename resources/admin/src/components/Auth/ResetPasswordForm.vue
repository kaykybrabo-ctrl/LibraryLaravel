<template>
  <div>
    <h2 v-if="!resetToken">Recuperar senha</h2>
    <h2 v-else>Definir nova senha</h2>

    <form v-if="!resetToken" @submit.prevent="$emit('requestReset')">
      <div class="form-group">
        <label>Email cadastrado:</label>
        <input
          type="email"
          :value="forgotEmail"
          @input="$emit('update:forgotEmail', $event.target.value)"
          required
        >
      </div>
      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 4px;">
        Enviar link de redefinição
      </button>
    </form>

    <form v-else @submit.prevent="$emit('submitNewPassword')">
      <p style="margin-bottom:10px; font-size:0.9rem; color:#555;">
        E-mail: <strong>{{ resetEmail }}</strong>
      </p>
      <div class="form-group">
        <label>Nova senha:</label>
        <div style="display:flex; gap:8px; align-items:center;">
          <input
            :type="resetPasswordVisible ? 'text' : 'password'"
            :value="resetNewPassword"
            @input="$emit('update:resetNewPassword', $event.target.value)"
            required
            style="flex: 1;"
          >
          <button
            type="button"
            class="btn btn-small btn-secondary"
            style="white-space: nowrap;"
            @click="$emit('update:resetPasswordVisible', !resetPasswordVisible)"
          >
            {{ resetPasswordVisible ? 'Ocultar' : 'Mostrar' }}
          </button>
        </div>
      </div>

      <div class="form-group">
        <label>Confirmar nova senha:</label>
        <div style="display:flex; gap:8px; align-items:center;">
          <input
            :type="resetPasswordConfirmVisible ? 'text' : 'password'"
            :value="resetNewPasswordConfirm"
            @input="$emit('update:resetNewPasswordConfirm', $event.target.value)"
            required
            style="flex: 1;"
          >
          <button
            type="button"
            class="btn btn-small btn-secondary"
            style="white-space: nowrap;"
            @click="$emit('update:resetPasswordConfirmVisible', !resetPasswordConfirmVisible)"
          >
            {{ resetPasswordConfirmVisible ? 'Ocultar' : 'Mostrar' }}
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 4px;">
        Salvar nova senha
      </button>
    </form>

    <p class="text-center mt-3">
      <a href="#" @click.prevent="$emit('backToLogin')">Voltar para login</a>
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
  name: 'ResetPasswordForm',
  props: {
    resetToken: { type: String, required: true },
    resetEmail: { type: String, required: true },

    forgotEmail: { type: String, required: true },

    resetNewPassword: { type: String, required: true },
    resetNewPasswordConfirm: { type: String, required: true },

    resetPasswordVisible: { type: Boolean, required: true },
    resetPasswordConfirmVisible: { type: Boolean, required: true },
  },
};
</script>
