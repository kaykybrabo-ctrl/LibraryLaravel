export default {
  methods: {
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
        const bookId = book && book.id != null ? String(book.id) : null;
        if (!bookId) return;
        const loan = Array.isArray(this.userLoans)
          ? this.userLoans.find((l) => {
            if (!l || l.returned_at) return false;
            const loanBookId = l.book_id != null ? l.book_id : (l.book && l.book.id != null ? l.book.id : null);
            if (loanBookId == null || loanBookId === '') return false;
            return String(loanBookId) === bookId;
          })
          : null;

        if (!loan || !loan.id) return;
        this.promptReturn(loan.id);
      } else {
        this.openRentModal(book);
      }
    },
  },
};
