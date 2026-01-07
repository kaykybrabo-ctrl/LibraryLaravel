<template>
  <div>
    <h2>{{ $t('auth.loginHeading') }}</h2>
    <form @submit.prevent="$emit('submit')">
      <div class="form-group">
        <label>{{ $t('auth.email') }}:</label>
        <input
          type="email"
          :value="loginEmail"
          @input="$emit('update:loginEmail', $event.target.value)"
          required
        >
      </div>
      <div class="form-group">
        <label>{{ $t('auth.password') }}:</label>
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
            {{ loginPasswordVisible ? $t('auth.hidePassword') : $t('auth.showPassword') }}
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 4px;">
        {{ $t('auth.login') }}
      </button>
    </form>

    <p class="text-center mt-2">
      <a href="#" @click.prevent="$emit('openReset')">{{ $t('auth.forgotPassword') }}</a>
    </p>
    <p class="text-center mt-3">
      {{ $t('auth.noAccountQuestion') }}
      <a href="#" @click.prevent="$emit('showRegister')">{{ $t('auth.register') }}</a>
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
  name: 'LoginForm',
  props: {
    loginEmail: { type: String, required: true },
    loginPassword: { type: String, required: true },
    loginPasswordVisible: { type: Boolean, required: true },
  },
};
</script>
