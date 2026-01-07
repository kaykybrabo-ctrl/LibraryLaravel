<template>
  <div>
    <h2>{{ $t('auth.register') }}</h2>
    <form @submit.prevent="$emit('submit')">
      <div class="form-group">
        <label>{{ $t('auth.name') }}:</label>
        <input
          type="text"
          :value="registerName"
          @input="$emit('update:registerName', $event.target.value)"
          required
        >
      </div>
      <div class="form-group">
        <label>{{ $t('auth.email') }}:</label>
        <input
          type="email"
          :value="registerEmail"
          @input="$emit('update:registerEmail', $event.target.value)"
          required
        >
      </div>
      <div class="form-group">
        <label>{{ $t('auth.password') }}:</label>
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
            {{ registerPasswordVisible ? $t('auth.hidePassword') : $t('auth.showPassword') }}
          </button>
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
            style="flex: 1;"
          >
          <button
            type="button"
            class="btn btn-small btn-secondary"
            style="white-space: nowrap;"
            @click="$emit('update:registerPasswordVisible', !registerPasswordVisible)"
          >
            {{ registerPasswordVisible ? $t('auth.hidePassword') : $t('auth.showPassword') }}
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 4px;">
        {{ $t('auth.register') }}
      </button>
    </form>

    <p class="text-center mt-3">
      {{ $t('auth.alreadyHaveAccountQuestion') }}
      <a href="#" @click.prevent="$emit('showLogin')">{{ $t('auth.login') }}</a>
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
