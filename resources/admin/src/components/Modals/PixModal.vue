<template>
  <div class="modal" :class="{ active: show }" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3 style="color:#162c74; font-weight:600;">Pagamento PIX Simulado</h3>
        <button class="modal-close" @click="$emit('close')">✕</button>
      </div>
      <div class="modal-body">
        <div style="display:flex; flex-direction:column; gap:12px;">
          <div style="background:#f8f9fa; border-radius:8px; padding:12px;">
            <div style="color:#666; line-height:1.5;">Valor</div>
            <div style="font-size:1.25rem; font-weight:700; color:#111827; margin-top:4px;">{{ pixAmountFormatted }}</div>
          </div>

          <p style="color:#666; line-height:1.5; margin:0;">
            Use o código fictício abaixo como se fosse um PIX. Após "pagar", clique em <strong>Confirmar pagamento</strong>.
          </p>

          <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
            <div style="color:#666; font-weight:600;">Código PIX (copia e cola)</div>
            <button class="btn btn-small btn-secondary" type="button" @click="copyPixCode" style="width:auto;">Copiar</button>
          </div>

          <div
            style="background:#111827; color:#f9fafb; padding:14px; border-radius:8px; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace; word-break:break-all; line-height:1.5; max-height:140px; overflow:auto;"
          >
            {{ pixPayload }}
          </div>

          <p style="color:#777; font-size:0.85rem; margin:0;">
            Ambiente de testes. Nenhum pagamento real é realizado.
          </p>

          <div style="display:flex; gap:10px; margin-top:4px;">
            <button class="btn btn-small" @click="$emit('confirm')">Confirmar pagamento</button>
            <button class="btn btn-small btn-secondary" @click="$emit('close')">Cancelar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PixModal',
  props: {
    show: { type: Boolean, required: true },
    pixCode: { type: String, required: true },
    pixAmountFormatted: { type: String, required: true },
  },
  computed: {
    pixPayload() {
      const rawRef = (this.pixCode || '').trim();
      const ref = rawRef.replace(/\s+/g, '').slice(0, 25) || 'PEDIDO_SIMULADO';

      const parsed = String(this.pixAmountFormatted || '').replace(/[^0-9,\.]/g, '').replace(',', '.');
      const amountNumber = Number(parsed);
      const amount = Number.isFinite(amountNumber) ? amountNumber.toFixed(2) : '0.00';

      const pad2 = (n) => String(n).padStart(2, '0');
      const f = (id, value) => `${id}${pad2(String(value).length)}${value}`;

      const gui = f('00', 'BR.GOV.BCB.PIX');
      const key = f('01', 'simulado@library.local');
      const desc = f('02', `REF:${ref}`);
      const mai = f('26', `${gui}${key}${desc}`);

      const merchantAccount = mai;
      const payloadBase =
        f('00', '01') +
        f('01', '12') +
        merchantAccount +
        f('52', '0000') +
        f('53', '986') +
        f('54', amount) +
        f('58', 'BR') +
        f('59', 'BIBLIOTECA') +
        f('60', 'SAO PAULO') +
        f('62', f('05', ref));

      return `${payloadBase}6304FFFF`;
    },
  },
  methods: {
    async copyPixCode() {
      const text = (this.pixPayload || '').trim();
      if (!text) return;

      try {
        if (navigator && navigator.clipboard && navigator.clipboard.writeText) {
          await navigator.clipboard.writeText(text);
          return;
        }

        const ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
      } catch (e) {
      }
    },
  },
};
</script>
