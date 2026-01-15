<template>
  <div>
    <h2>{{ $t('auth.loginHeading') }}</h2>
    <form @submit.prevent="handleSubmit" novalidate>
      <div class="form-group">
        <label>{{ $t('auth.email') }}:</label>
        <input
          type="email"
          :value="loginEmail"
          @input="$emit('update:loginEmail', $event.target.value)"
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
            :type="loginPasswordVisible ? 'text' : 'password'"
            :value="loginPassword"
            @input="$emit('update:loginPassword', $event.target.value)"
            required
            :disabled="loading"
            style="flex: 1;"
          >
          <button
            type="button"
            class="btn btn-small btn-secondary"
            style="white-space: nowrap;"
            :disabled="loading"
            @click="$emit('update:loginPasswordVisible', !loginPasswordVisible)"
          >
            {{ loginPasswordVisible ? $t('auth.hidePassword') : $t('auth.showPassword') }}
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
      <button
        type="submit"
        class="btn btn-primary"
        style="width: 100%; margin-top: 4px;"
        :disabled="loading"
      >
        <LoadingSpinner v-if="loading" inline :text="$t('auth.loggingIn')" />
        <span v-else>{{ $t('auth.login') }}</span>
      </button>
    </form>

    <p class="text-center mt-2">
      <a href="#" :style="loading ? 'pointer-events:none; opacity:0.6;' : ''" @click.prevent="loading ? null : $emit('openReset')">{{ $t('auth.forgotPassword') }}</a>
    </p>
    <p class="text-center mt-3">
      {{ $t('auth.noAccountQuestion') }}
      <a href="#" :style="loading ? 'pointer-events:none; opacity:0.6;' : ''" @click.prevent="loading ? null : $emit('showRegister')">{{ $t('auth.register') }}</a>
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
  name: 'LoginForm',
  components: { LoadingSpinner },
  props: {
    loginEmail: { type: String, required: true },
    loginPassword: { type: String, required: true },
    loginPasswordVisible: { type: Boolean, required: true },
    loading: { type: Boolean, default: false },
    fieldErrors: { type: Object, default: () => ({}) },
  },
  data() {
    return {
      localErrors: {
        email: '',
        password: '',
      },
    };
  },
  methods: {
    handleSubmit() {
      // reset local errors
      this.localErrors.email = '';
      this.localErrors.password = '';

      // simple client-side validation to avoid submits obviamente inválidos
      const email = (this.loginEmail || '').trim();
      const password = this.loginPassword || '';

      // very simple email regex just to catch erros gritantes
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!email) {
        this.localErrors.email = this.$t ? this.$t('validation.required') || 'Email é obrigatório' : 'Email é obrigatório';
      } else if (!emailRegex.test(email)) {
        this.localErrors.email = this.$t ? this.$t('validation.email') || 'Email inválido' : 'Email inválido';
      }

      if (!password) {
        this.localErrors.password = this.$t ? this.$t('validation.required') || 'Senha é obrigatória' : 'Senha é obrigatória';
      }

      if (this.localErrors.email || this.localErrors.password) {
        if (typeof window !== 'undefined' && window.$uiStore) {
          const msg = this.$t
            ? this.$t('errors.loginFailed')
            : 'Erro ao fazer login. Verifique os campos.';
          window.$uiStore.showToast('error', msg);
        }
        return;
      }

      this.$emit('submit');
    },
  },
};
</script>
