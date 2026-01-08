<template>
  <div>
    <h2 v-if="!resetToken">{{ $t('auth.recoverPasswordTitle') }}</h2>
    <h2 v-else>{{ $t('auth.setNewPasswordTitle') }}</h2>

    <form v-if="!resetToken" @submit.prevent="handleRequestReset">
      <div class="form-group">
        <label>{{ $t('auth.registeredEmail') }}:</label>
        <input
          type="email"
          :value="forgotEmail"
          @input="$emit('update:forgotEmail', $event.target.value)"
          required
          :disabled="loading"
        >
        <div
          v-if="localErrors.email"
          style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
        >
          {{ localErrors.email }}
        </div>
        <div
          v-else-if="fieldErrors && fieldErrors.email && fieldErrors.email.length"
          style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
        >
          {{ fieldErrors.email[0] }}
        </div>
      </div>
      <button
        type="submit"
        class="btn btn-primary"
        style="width: 100%; margin-top: 4px;"
        :disabled="loading"
      >
        <LoadingSpinner v-if="loading" inline :text="$t('common.sending')" />
        <span v-else>{{ $t('auth.sendResetLink') }}</span>
      </button>
    </form>

    <form v-else @submit.prevent="handleSubmitNewPassword">
      <p style="margin-bottom:10px; font-size:0.9rem; color:#555;">
        {{ $t('auth.email') }}: <strong>{{ resetEmail }}</strong>
      </p>
      <div
        v-if="fieldErrors && fieldErrors.email && fieldErrors.email.length"
        style="color:#dc3545; font-size:0.85rem; margin-top:-6px; margin-bottom:10px;"
      >
        {{ fieldErrors.email[0] }}
      </div>
      <div class="form-group">
        <label>{{ $t('auth.newPassword') }}:</label>
        <div style="display:flex; gap:8px; align-items:center;">
          <input
            :type="resetPasswordVisible ? 'text' : 'password'"
            :value="resetNewPassword"
            @input="$emit('update:resetNewPassword', $event.target.value)"
            required
            :disabled="loading"
            style="flex: 1;"
          >
          <button
            type="button"
            class="btn btn-small btn-secondary"
            style="white-space: nowrap;"
            :disabled="loading"
            @click="$emit('update:resetPasswordVisible', !resetPasswordVisible)"
          >
            {{ resetPasswordVisible ? $t('auth.hidePassword') : $t('auth.showPassword') }}
          </button>
        </div>
        <div
          v-if="localErrors.password"
          style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
        >
          {{ localErrors.password }}
        </div>
        <div
          v-else-if="fieldErrors && fieldErrors.password && fieldErrors.password.length"
          style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
        >
          {{ fieldErrors.password[0] }}
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
            :disabled="loading"
            style="flex: 1;"
          >
          <button
            type="button"
            class="btn btn-small btn-secondary"
            style="white-space: nowrap;"
            :disabled="loading"
            @click="$emit('update:resetPasswordConfirmVisible', !resetPasswordConfirmVisible)"
          >
            {{ resetPasswordConfirmVisible ? $t('auth.hidePassword') : $t('auth.showPassword') }}
          </button>
        </div>
        <div
          v-if="localErrors.password_confirmation"
          style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
        >
          {{ localErrors.password_confirmation }}
        </div>
        <div
          v-else-if="fieldErrors && fieldErrors.password_confirmation && fieldErrors.password_confirmation.length"
          style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
        >
          {{ fieldErrors.password_confirmation[0] }}
        </div>
      </div>

      <button
        type="submit"
        class="btn btn-primary"
        style="width: 100%; margin-top: 4px;"
        :disabled="loading"
      >
        <LoadingSpinner v-if="loading" inline :text="$t('common.loading')" />
        <span v-else>{{ $t('auth.saveNewPassword') }}</span>
      </button>
    </form>

    <p class="text-center mt-3">
      <a href="#" :style="loading ? 'pointer-events:none; opacity:0.6;' : ''" @click.prevent="loading ? null : $emit('backToLogin')">{{ $t('auth.backToLogin') }}</a>
    </p>
    <button
      class="btn btn-secondary"
      style="margin-top: 10px; width: 100%;"
      :disabled="loading"
      @click="$emit('goToLanding')"
    >
      {{ $t('auth.backToHome') }}
    </button>
  </div>
</template>

<script>
import LoadingSpinner from '../common/LoadingSpinner.vue';

export default {
  name: 'ResetPasswordForm',
  components: { LoadingSpinner },
  props: {
    resetToken: { type: String, required: true },
    resetEmail: { type: String, required: true },

    forgotEmail: { type: String, required: true },

    resetNewPassword: { type: String, required: true },
    resetNewPasswordConfirm: { type: String, required: true },

    resetPasswordVisible: { type: Boolean, required: true },
    resetPasswordConfirmVisible: { type: Boolean, required: true },

    loading: { type: Boolean, default: false },
    fieldErrors: { type: Object, default: () => ({}) },
  },
  data() {
    return {
      localErrors: {
        email: '',
        password: '',
        password_confirmation: '',
      },
    };
  },
  methods: {
    handleRequestReset() {
      this.localErrors.email = '';
      const email = (this.forgotEmail || '').trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!email) {
        this.localErrors.email = this.$t ? this.$t('validation.required') || 'Email é obrigatório' : 'Email é obrigatório';
      } else if (!emailRegex.test(email)) {
        this.localErrors.email = this.$t ? this.$t('validation.email') || 'Email inválido' : 'Email inválido';
      }

      if (this.localErrors.email) {
        return;
      }

      this.$emit('requestReset');
    },
    handleSubmitNewPassword() {
      this.localErrors.password = '';
      this.localErrors.password_confirmation = '';

      const password = this.resetNewPassword || '';
      const passwordConfirmation = this.resetNewPasswordConfirm || '';

      if (!password) {
        this.localErrors.password = this.$t ? this.$t('validation.required') || 'Senha é obrigatória' : 'Senha é obrigatória';
      } else if (password.length < 8) {
        this.localErrors.password = this.$t ? this.$t('validation.min.string', { attribute: 'password', min: 8 }) || 'Senha deve ter pelo menos 8 caracteres' : 'Senha deve ter pelo menos 8 caracteres';
      }

      if (!passwordConfirmation) {
        this.localErrors.password_confirmation = this.$t ? this.$t('validation.required') || 'Confirmação de senha é obrigatória' : 'Confirmação de senha é obrigatória';
      } else if (passwordConfirmation !== password) {
        this.localErrors.password_confirmation = this.$t ? this.$t('validation.confirmed') || 'As senhas não conferem' : 'As senhas não conferem';
      }

      if (this.localErrors.password || this.localErrors.password_confirmation) {
        return;
      }

      this.$emit('submitNewPassword');
    },
  },
};
</script>
