export default {
  methods: {
    async addToCart(book) {
      if (!book || !this.currentUser) return;
      const bookId = book && book.id != null ? Number(book.id) : null;
      if (!Number.isFinite(bookId) || bookId <= 0) {
        this.errorMessage = this.$t('errors.invalidBook');
        return;
      }
      try {
        this.errorMessage = '';
        this.successMessage = '';
        await this.graphql(
          'mutation UpsertCartItem($bookId: ID!, $quantity: Int!) { upsertCartItem(book_id: $bookId, quantity: $quantity) { id quantity } }',
          { bookId, quantity: 1 }
        );
        await this.loadCart();
        this.successMessage = this.$t('messages.bookAddedToCart');
      } catch (e) {
        this.errorMessage = `${this.$t('errors.addToCartFailed')} ${e && e.message ? e.message : ''}`.trim();
      }
    },

    async changeCartQuantity(itemId, quantity) {
      try {
        const q = Number(quantity);
        if (!Number.isFinite(q)) return;
        if (q <= 0) {
          await this.removeFromCart(itemId);
          return;
        }
        const item = Array.isArray(this.cart) ? this.cart.find((i) => i && i.id === itemId) : null;
        const bookId = item && item.book ? item.book.id : null;
        if (!bookId) return;
        await this.graphql(
          'mutation UpsertCartItem($bookId: ID!, $quantity: Int!) { upsertCartItem(book_id: $bookId, quantity: $quantity) { id quantity } }',
          { bookId, quantity: q }
        );
        await this.loadCart();
      } catch (e) {

      }
    },

    async removeFromCart(itemId) {
      try {
        const item = Array.isArray(this.cart) ? this.cart.find((i) => i && i.id === itemId) : null;
        const bookId = item && item.book ? item.book.id : null;
        if (!bookId) return;
        await this.graphql(
          'mutation RemoveCartItem($bookId: ID!) { removeCartItem(book_id: $bookId) { message } }',
          { bookId }
        );
        await this.loadCart();
      } catch (e) {

      }
    },

    async clearCart() {
      try {
        await this.graphql('mutation { clearCart { message } }');
        await this.loadCart();
      } catch (e) {

      }
    },

    async handleCheckout() {
      if (!this.cart.length) return;
      try {
        this.errorMessage = '';
        this.successMessage = '';
        const items = (Array.isArray(this.cart) ? this.cart : [])
          .map((i) => ({
            book_id: i && i.book ? i.book.id : null,
            quantity: i && typeof i.quantity === 'number' ? i.quantity : 1,
          }))
          .filter((i) => i.book_id != null);

        if (!items.length) return;

        const data = await this.graphql(
          'mutation Checkout($input: CheckoutInput!) { checkout(input: $input) { id total status } }',
          { input: { items } }
        );
        if (data && data.checkout) {
          this.pixAmount = Number(data.checkout.total) || 0;
          this.cart = [];
          this.pixCode = `PIX_ORDER_${data.checkout.id}_${Date.now()}`;
          this.showPixModal = true;
          this.successMessage = this.$t('messages.orderCreatedPix');
        }
      } catch (e) {

        this.errorMessage = this.$t('errors.checkoutFailed');
      }
    },

    closePixModal() {
      this.showPixModal = false;
      this.pixCode = '';
      this.pixAmount = 0;
    },

    async confirmPixPayment() {
      try {
        this.errorMessage = '';
        this.successMessage = this.$t('messages.checkoutSuccess');
        this.closePixModal();
      } catch (e) {

      }
    },
  },
};
