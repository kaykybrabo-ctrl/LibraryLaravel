export default {
  computed: {
    cartTotal() {
      return this.cart.reduce((sum, item) => sum + (item.book ? (item.book.price || 0) * item.quantity : 0), 0);
    },

    cartTotalFormatted() {
      return 'R$ ' + this.cartTotal.toFixed(2).replace('.', ',');
    },

    pixAmountFormatted() {
      const hasPixAmount = this.showPixModal && Number.isFinite(Number(this.pixAmount));
      const amount = hasPixAmount ? Number(this.pixAmount) : this.cartTotal;
      return 'R$ ' + amount.toFixed(2).replace('.', ',');
    },
  },
};
