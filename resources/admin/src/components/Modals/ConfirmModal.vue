<template>
  <div class="modal" :class="{ active: show }" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3 style="color:#162c74; font-weight:600;">{{ title || 'Confirmar ação' }}</h3>
        <button class="modal-close" @click="$emit('close')">✕</button>
      </div>
      <div class="modal-body">
        <p style="color:#666; line-height:1.5;">{{ message }}</p>
        <div style="display:flex; gap:10px; margin-top:12px;">
          <button
            class="btn btn-small"
            :class="{ 'btn-danger': isDanger }"
            @click="$emit('confirm')"
          >
            {{ confirmLabel || 'Confirmar' }}
          </button>
          <button class="btn btn-small btn-secondary" @click="$emit('close')">
            {{ cancelLabel || 'Cancelar' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ConfirmModal',
  props: {
    show: { type: Boolean, required: true },
    title: { type: String, default: '' },
    message: { type: String, default: '' },
    confirmLabel: { type: String, default: '' },
    cancelLabel: { type: String, default: '' },
    isDanger: { type: Boolean, required: true },
  },
};
</script>
