<template>
  <div>
    <h2 v-if="!resetToken">{{ $t('auth.recoverPasswordTitle') }}</h2>
    <h2 v-else>{{ $t('auth.setNewPasswordTitle') }}</h2>

    <form v-if="!resetToken" @submit.prevent="$emit('requestReset')">
      <div class="form-group">
        <label>{{ $t('auth.registeredEmail') }}:</label>
        <input
          type="email"
          :value="forgotEmail"
          @input="$emit('update:forgotEmail', $event.target.value)"
          required
        >
      </div>
      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 4px;">
        {{ $t('auth.sendResetLink') }}
      </button>
    </form>

    <form v-else @submit.prevent="$emit('submitNewPassword')">
      <p style="margin-bottom:10px; font-size:0.9rem; color:#555;">
        {{ $t('auth.email') }}: <strong>{{ resetEmail }}</strong>
      </p>
      <div class="form-group">
        <label>{{ $t('auth.newPassword') }}:</label>
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
            {{ resetPasswordVisible ? $t('auth.hidePassword') : $t('auth.showPassword') }}
          </button>
        </div>
      </div>

      <div class="form-group">
        <label>{{ $t('auth.confirmNewPassword') }}:</label>
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
            {{ resetPasswordConfirmVisible ? $t('auth.hidePassword') : $t('auth.showPassword') }}
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 4px;">
        {{ $t('auth.saveNewPassword') }}
      </button>
    </form>

    <p class="text-center mt-3">
      <a href="#" @click.prevent="$emit('backToLogin')">{{ $t('auth.backToLogin') }}</a>
    </p>
    <button
      class="btn btn-secondary"
      style="margin-top: 10px; width: 100%;"
      @click="$emit('goToLanding')"
    >
      {{ $t('auth.backToHome') }}
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
