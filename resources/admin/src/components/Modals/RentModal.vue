<template>
  <div class="modal" :class="{ active: show }" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Alugar Livro</h3>
        <button class="modal-close" @click="$emit('close')">✕</button>
      </div>
      <div class="modal-body">
        <p v-if="rentTargetBook" style="margin-bottom:12px;color:#162c74;font-weight:600;">{{ rentTargetBook.title }}</p>
        <div class="form-group">
          <label>Data de devolução:</label>
          <input
            type="date"
            :value="rentReturnDate"
            :min="minEndDate"
            :max="maxDate2030"
            @input="$emit('update:rentReturnDate', $event.target.value)"
          >
        </div>

        <div
          v-if="validationMessage"
          style="margin-top:8px; padding:10px 12px; border-radius:8px; background:#fff5f5; border:1px solid #fecaca; color:#b91c1c; font-size:0.9rem;"
        >
          {{ validationMessage }}
        </div>

        <div style="display:flex; gap:10px; margin-top:10px;">
          <button class="btn btn-small" :disabled="!canConfirm" @click="onConfirm">Confirmar Aluguel</button>
          <button class="btn btn-small btn-secondary" @click="$emit('close')">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RentModal',
  props: {
    show: { type: Boolean, required: true },
    rentTargetBook: { type: Object, default: null },
    rentReturnDate: { type: String, required: true },
    minEndDate: { type: String, required: true },
    maxDate2030: { type: String, required: true },
  },

  computed: {
    canConfirm() {
      const v = String(this.rentReturnDate || '').trim();
      if (!v) return false;
      if (this.minEndDate && v < this.minEndDate) return false;
      if (this.maxDate2030 && v > this.maxDate2030) return false;
      return true;
    },

    validationMessage() {
      const v = String(this.rentReturnDate || '').trim();
      if (!v) return 'Selecione uma data de devolução.';
      if (this.minEndDate && v < this.minEndDate) return 'Não é permitido alugar para uma data antes de hoje.';
      if (this.maxDate2030 && v > this.maxDate2030) return 'A data máxima permitida é 2030.';
      return '';
    },
  },

  methods: {
    onConfirm() {
      if (!this.canConfirm) return;
      this.$emit('confirm');
    },
  },
};
</script>
