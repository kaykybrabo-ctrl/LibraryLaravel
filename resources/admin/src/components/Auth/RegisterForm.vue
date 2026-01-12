<template>
  <div>
    <h2>{{ $t('auth.register') }}</h2>
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label>{{ $t('auth.name') }}:</label>
        <input
          type="text"
          :value="registerName"
          @input="$emit('update:registerName', $event.target.value)"
          required
          :disabled="loading"
        >
        <div
          v-if="localErrors.name"
          style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
        >
          {{ localErrors.name }}
        </div>
        <div
          v-else-if="fieldErrors && fieldErrors.name && fieldErrors.name.length"
          style="color:#dc3545; font-size:0.85rem; margin-top:4px;"
        >
          {{ fieldErrors.name[0] }}
        </div>
      </div>
      <div class="form-group">
        <label>{{ $t('auth.email') }}:</label>
        <input
          type="email"
          :value="registerEmail"
          @input="$emit('update:registerEmail', $event.target.value)"
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
      <div class="form-group">
        <label>{{ $t('auth.password') }}:</label>
        <div style="display:flex; gap:8px; align-items:center;">
          <input
            :type="registerPasswordVisible ? 'text' : 'password'"
            :value="registerPassword"
            @input="$emit('update:registerPassword', $event.target.value)"
            required
            :disabled="loading"
            style="flex: 1;"
          >
          <button
            type="button"
            class="btn btn-small btn-secondary"
            style="white-space: nowrap;"
            :disabled="loading"
            @click="$emit('update:registerPasswordVisible', !registerPasswordVisible)"
          >
            {{ registerPasswordVisible ? $t('auth.hidePassword') : $t('auth.showPassword') }}
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
        <label>{{ $t('auth.confirmPassword') }}:</label>
        <div style="display:flex; gap:8px; align-items:center;">
          <input
            :type="registerPasswordVisible ? 'text' : 'password'"
            :value="registerPasswordConfirmation"
            @input="$emit('update:registerPasswordConfirmation', $event.target.value)"
            required
            :disabled="loading"
            style="flex: 1;"
          >
          <button
            type="button"
            class="btn btn-small btn-secondary"
            style="white-space: nowrap;"
            :disabled="loading"
            @click="$emit('update:registerPasswordVisible', !registerPasswordVisible)"
          >
            {{ registerPasswordVisible ? $t('auth.hidePassword') : $t('auth.showPassword') }}
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
        <LoadingSpinner v-if="loading" inline :text="$t('auth.registering')" />
        <span v-else>{{ $t('auth.register') }}</span>
      </button>
    </form>

    <p class="text-center mt-3">
      {{ $t('auth.alreadyHaveAccountQuestion') }}
      <a href="#" :style="loading ? 'pointer-events:none; opacity:0.6;' : ''" @click.prevent="loading ? null : $emit('showLogin')">{{ $t('auth.login') }}</a>
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
  name: 'RegisterForm',
  components: { LoadingSpinner },
  props: {
    registerName: { type: String, required: true },
    registerEmail: { type: String, required: true },
    registerPassword: { type: String, required: true },
    registerPasswordConfirmation: { type: String, required: true },
    registerPasswordVisible: { type: Boolean, required: true },
    loading: { type: Boolean, default: false },
    fieldErrors: { type: Object, default: () => ({}) },
  },
  data() {
    return {
      localErrors: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
      },
    };
  },
  methods: {
    handleSubmit() {
      this.localErrors.name = '';
      this.localErrors.email = '';
      this.localErrors.password = '';
      this.localErrors.password_confirmation = '';

      const name = (this.registerName || '').trim();
      const email = (this.registerEmail || '').trim();
      const password = this.registerPassword || '';
      const passwordConfirmation = this.registerPasswordConfirmation || '';

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!name) {
        this.localErrors.name = this.$t ? this.$t('validation.required') || 'Nome é obrigatório' : 'Nome é obrigatório';
      }

      if (!email) {
        this.localErrors.email = this.$t ? this.$t('validation.required') || 'Email é obrigatório' : 'Email é obrigatório';
      } else if (!emailRegex.test(email)) {
        this.localErrors.email = this.$t ? this.$t('validation.email') || 'Email inválido' : 'Email inválido';
      }

      if (!password) {
        this.localErrors.password = this.$t ? this.$t('validation.required') || 'Senha é obrigatória' : 'Senha é obrigatória';
      } else if (password.length < 8) {
        this.localErrors.password = 'Senha deve ter pelo menos 8 caracteres';
      }

      if (!passwordConfirmation) {
        this.localErrors.password_confirmation = this.$t ? this.$t('validation.required') || 'Confirmação de senha é obrigatória' : 'Confirmação de senha é obrigatória';
      } else if (passwordConfirmation !== password) {
        this.localErrors.password_confirmation = this.$t ? this.$t('validation.confirmed') || 'As senhas não conferem' : 'As senhas não conferem';
      }

      if (this.localErrors.name || this.localErrors.email || this.localErrors.password || this.localErrors.password_confirmation) {
        return;
      }

      this.$emit('submit');
    },
  },
};
</script>
