export default {
  methods: {
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.booksPage = page;
        if (typeof this.loadBooks === 'function') {
          this.loadBooks();
        }
        if (typeof window !== 'undefined') {
          window.scrollTo(0, 0);
        }
      }
    },

    changeAuthorsPage(page) {
      if (page >= 1 && page <= this.totalAuthorsPages) {
        this.authorsPage = page;
        if (typeof this.loadAuthorsPage === 'function') {
          this.loadAuthorsPage();
        }
        if (typeof window !== 'undefined') {
          window.scrollTo(0, 0);
        }
      }
    },

    async selectAuthor(author) {
      if (!author) return;
      const authorId = author && author.id != null ? Number(author.id) : null;
      if (!Number.isFinite(authorId) || authorId <= 0) return;
      this.selectedAuthor = author;
      this.routePage = 'author-detail';
      if (typeof window !== 'undefined') {
        window.location.hash = `author/${authorId}`;
      }
    },

    viewBook(book) {
      this.selectedBook = book;
      this.routePage = 'book-detail';
      if (typeof window !== 'undefined') {
        window.location.hash = `book/${book.id}`;
      }
    },

    async toggleFavorite(book) {
      if (!book || !this.currentUser) return;
      try {
        await this.graphql(
          'mutation ToggleFavorite($input: ToggleFavoriteInput!) { toggleFavorite(input: $input) { id } }',
          { input: { user_id: this.currentUser.id, book_id: book.id } }
        );
        await this.loadFavoriteBook();
      } catch (e) {

      }
    },

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

    async openRentModal(book) {
      if (!book || !this.currentUser) return;
      this.rentTargetBook = book;
      this.rentReturnDate = '';
      this.showRentModal = true;
    },

    closeRentModal() {
      this.showRentModal = false;
      this.rentTargetBook = null;
      this.rentReturnDate = '';
    },

    async confirmRent() {
      if (!this.rentTargetBook || !this.rentReturnDate) return;
      try {
        const loanDate = new Date().toISOString().slice(0, 10);
        await this.graphql(
          'mutation RentBook($input: RentBookInput!) { rentBook(input: $input) { id } }',
          {
            input: {
              user_id: this.currentUser.id,
              book_id: this.rentTargetBook.id,
              loan_date: loanDate,
              return_date: this.rentReturnDate,
            },
          }
        );
        await this.loadUserLoans();
        await this.loadActiveBookIds();
        await this.loadBooks();
        this.closeRentModal();
      } catch (e) {

      }
    },

    async promptReturn(loanId) {
      this.askDelete('loan', this.$t('loans.confirmReturn'), () => this.returnBook(loanId));
    },

    async returnBook(loanId) {
      try {
        this.errorMessage = '';
        this.successMessage = '';
        await this.graphql(
          'mutation ReturnBook($id: ID!) { returnBook(id: $id) { id } }',
          { id: loanId }
        );
        await this.loadUserLoans();
        await this.loadActiveBookIds();
        await this.loadBooks();
        this.successMessage = this.$t('messages.bookReturned');
      } catch (e) {

        this.errorMessage = this.$t('errors.returnFailed');
      }
    },

    async confirmReturnMyLoan(loanId) {
      try {
        this.errorMessage = '';
        this.successMessage = '';
        await this.graphql(
          'mutation ReturnBook($id: ID!) { returnBook(id: $id) { id } }',
          { id: loanId }
        );
        await this.loadUserLoans();
        await this.loadActiveBookIds();
        this.successMessage = this.$t('messages.bookReturned');
      } catch (e) {

        this.errorMessage = this.$t('errors.returnFailed');
      }
    },

    handleBookRentOrReturn(book) {
      if (this.isBookBorrowedByMe(book)) {
        const loan = Array.isArray(this.userLoans)
          ? this.userLoans.find(l => (l.book_id === book.id || (l.book && l.book.id === book.id)) && !l.returned_at)
          : null;

        if (!loan || !loan.id) return;
        this.promptReturn(loan.id);
      } else {
        this.openRentModal(book);
      }
    },
  },
};
