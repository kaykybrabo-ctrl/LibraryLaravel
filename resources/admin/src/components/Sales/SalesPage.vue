<template>
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h2>💰 Vendas (Pedidos)</h2>
    </div>
    <div v-if="adminOrdersLoading" class="text-center">Carregando vendas...</div>
    <div v-else>
      <div v-if="adminOrders && adminOrders.length > 0">
        <div v-for="order in adminOrders" :key="order.id" style="padding: 12px 16px; background: white; border-radius: 10px; margin-bottom: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;">
          <div style="display:flex; justify-content: space-between; align-items:flex-start; gap:12px;">
            <div>
              <div style="font-weight:600; color:#162c74; font-size:0.98rem;">Pedido #{{ order.id }}</div>
              <div style="color:#666; font-size:0.9rem; margin-top:4px;" v-if="order.user">
                Cliente: {{ order.user.name }} ({{ order.user.email }})
              </div>
              <div style="color:#666; font-size:0.85rem; margin-top:4px;">Data: {{ formatDate(order.created_at) }}</div>
            </div>
            <div style="text-align:right; min-width:140px;">
              <div style="font-size:0.9rem; color:#555;">Status: {{ formatOrderStatus(order.status) }}</div>
              <div style="font-size:1rem; font-weight:600; color:#162c74; margin-top:4px;">Total: {{ 'R$ ' + Number(order.total || 0).toFixed(2).replace('.', ',') }}</div>
            </div>
          </div>
          <div v-if="order.items && order.items.length" style="margin-top:8px; padding-top:8px; border-top:1px solid #e5e7eb; font-size:0.9rem; color:#444;">
            <div v-for="it in order.items" :key="it.id" style="display:flex; justify-content: space-between; gap:8px; margin-bottom:4px;">
              <div>
                <strong>{{ it.book && it.book.title ? it.book.title : 'Livro' }}</strong>
                <span style="color:#666;"> — {{ it.quantity }}x</span>
              </div>
              <div>
                {{ 'R$ ' + Number((it.unit_price || 0) * (it.quantity || 0)).toFixed(2).replace('.', ',') }}
              </div>
            </div>
          </div>
        </div>
      </div>
      <p v-else style="color:#666;">Nenhuma venda realizada ainda.</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SalesPage',
  props: {
    adminOrdersLoading: { type: Boolean, required: true },
    adminOrders: { type: Array, required: true },
    formatDate: { type: Function, required: true },
    formatOrderStatus: { type: Function, required: true },
  },
};
</script>
