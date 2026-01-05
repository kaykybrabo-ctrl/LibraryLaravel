<template>
  <div class="loading-spinner" :class="{ 'inline': inline }">
    <div class="spinner"></div>
    <span v-if="text" class="loading-text">{{ text }}</span>
  </div>
</template>

<script>
export default {
  name: 'LoadingSpinner',
  props: {
    text: String,
    inline: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style scoped>
.loading-spinner {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.loading-spinner.inline {
  display: inline-flex;
}

.spinner {
  width: 20px;
  height: 20px;
  border: 2px solid #f3f3f3;
  border-top: 2px solid #007bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.loading-text {
  color: #666;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
