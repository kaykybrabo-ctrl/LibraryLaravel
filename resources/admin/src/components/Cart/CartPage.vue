<template>
  <div class="container">
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
      <h2>{{ $t('cart.title') }}</h2>
      <button
        class="btn btn-small btn-secondary"
        style="width:auto;"
        @click="$emit('goBack')"
      >
        {{ $t('cart.backToBooks') }}
      </button>
    </div>

    <div v-if="!cartItems || cartItems.length === 0" style="color:#666;">{{ $t('cart.empty') }}</div>

    <div v-else>
      <div
        v-for="item in cartItems"
        :key="item.id"
        style="padding: 12px 16px; background: white; border-radius: 10px; margin-bottom: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;"
      >
        <div style="display:flex; justify-content: space-between; gap:12px; align-items:flex-start;">
          <div style="display:flex; gap:12px;">
            <div
              style="width:72px; height:96px; border-radius:6px; overflow:hidden; background:#f3f4f6; flex-shrink:0; cursor:pointer;"
              @click="item.book && $emit('viewBook', item.book)"
            >
              <img
                :src="thumb(item.book && item.book.photo ? item.book.photo : '', 300, 420, 'book')"
                :alt="item.book && item.book.title ? item.book.title : ''"
                @error="$event.target.src = thumb('', 300, 420, 'book')"
                style="width:100%; height:100%; object-fit:cover;"
              >
            </div>
            <div>
              <div style="font-weight:600; color:#162c74;">{{ item.book && item.book.title ? item.book.title : $t('loans.unknownBook') }}</div>
              <div style="color:#666; font-size:0.9rem; margin-top:4px;">
                {{ $t('cart.quantity') }}:
                <button
                  class="btn btn-small btn-secondary"
                  style="width:auto; padding:2px 8px; margin-right:4px;"
                  @click="$emit('changeQty', item.id, Math.max(0, (item.quantity || 0) - 1))"
                >
                  -
                </button>
                <span>{{ item.quantity }}</span>
                <button
                  class="btn btn-small btn-secondary"
                  style="width:auto; padding:2px 8px; margin-left:4px;"
                  @click="$emit('changeQty', item.id, (item.quantity || 0) + 1)"
                >
                  +
                </button>
              </div>
            </div>
          </div>

          <div style="text-align:right; min-width:160px;">
            <div style="color:#666; font-size:0.9rem;">{{ $t('cart.unitPrice') }}: {{ $formatCurrency(getBookPrice(item.book)) }}</div>
            <div style="color:#162c74; font-weight:700; margin-top:4px;">{{ $t('cart.subtotal') }}: {{ $formatCurrency(getBookPrice(item.book) * (item.quantity || 0)) }}</div>
            <button
              class="btn btn-small btn-danger"
              style="margin-top:6px; width:auto;"
              @click="$emit('remove', item.id)"
            >
              {{ $t('cart.removeItem') }}
            </button>
          </div>
        </div>
      </div>

      <div style="display:flex; justify-content: space-between; align-items:center; margin-top: 12px;">
        <div style="font-weight:700; color:#162c74;">{{ $t('cart.total') }}: {{ cartTotalFormatted }}</div>
        <div style="display:flex; gap:10px;">
          <button class="btn btn-small btn-secondary" @click="$emit('clear')">{{ $t('cart.clear') }}</button>
          <button class="btn btn-small" @click="$emit('checkout')">{{ $t('cart.checkout') }} ({{ $t('cart.pixPayment') }})</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CartPage',
  props: {
    cartItems: { type: Array, required: true },
    cartTotalFormatted: { type: String, required: true },
    getBookPrice: { type: Function, required: true },
    thumb: { type: Function, required: true },
  },
};
</script>
