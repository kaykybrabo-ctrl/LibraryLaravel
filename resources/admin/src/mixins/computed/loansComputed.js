export default {
  computed: {
    isBookUnavailable() {
      return (book) => {
        if (book == null) return true;
        const id = (typeof book === 'object' && book !== null) ? book.id : book;
        if (id == null || id === '') return true;
        const idStr = String(id);
        const ids = Array.isArray(this.activeBookIds) ? this.activeBookIds.map(String) : [];
        return ids.includes(idStr);
      };
    },

    isBookBorrowedByMe() {
      return (book) => {
        if (book == null) return false;
        const id = (typeof book === 'object' && book !== null) ? book.id : book;
        if (id == null || id === '') return false;
        const idStr = String(id);
        const loans = Array.isArray(this.userLoans) ? this.userLoans : [];
        return loans.some((loan) => {
          if (!loan || loan.returned_at) return false;
          const loanBookId = loan.book_id != null
            ? loan.book_id
            : (loan.book && loan.book.id != null ? loan.book.id : null);
          if (loanBookId == null || loanBookId === '') return false;
          return String(loanBookId) === idStr;
        });
      };
    },

    isFavorite() {
      return (book) => {
        if (book == null || !this.userFavoriteBook) return false;
        const id = (typeof book === 'object' && book !== null) ? book.id : book;
        if (id == null || id === '') return false;
        const idStr = String(id);
        const favId = this.userFavoriteBook && this.userFavoriteBook.id != null
          ? String(this.userFavoriteBook.id)
          : null;
        return favId === idStr;
      };
    },

    isLoanOverdue() {
      return (loan) => {
        if (!loan || !loan.return_date) return false;
        try {
          const due = new Date(loan.return_date);
          const today = new Date();
          today.setHours(0, 0, 0, 0);
          return due < today;
        } catch (e) {
          return false;
        }
      };
    },

    filteredAdminLoans() {
      let filtered = this.allLoans;
      if (this.adminLoansFilter === 'active') {
        filtered = filtered.filter(l => l.status === 'active');
      } else if (this.adminLoansFilter === 'overdue') {
        filtered = filtered.filter(l => l.status === 'overdue');
      } else if (this.adminLoansFilter === 'returned') {
        filtered = filtered.filter(l => l.status === 'returned');
      }
      return filtered;
    },

    filteredUserLoans() {
      let filtered = this.userLoans;
      if (this.userLoansFilter === 'active') {
        filtered = filtered.filter(l => !l.returned_at);
      } else if (this.userLoansFilter === 'returned') {
        filtered = filtered.filter(l => l.returned_at);
      } else if (this.userLoansFilter === 'overdue') {
        filtered = filtered.filter(l => !l.returned_at && this.isLoanOverdue(l));
      }
      return filtered;
    },

    selectedUserActiveLoans() {
      if (!this.selectedUser) return [];
      const list = Array.isArray(this.selectedUserLoans) && this.selectedUserLoans.length
        ? this.selectedUserLoans
        : (Array.isArray(this.allLoans) ? this.allLoans.filter(l => l.user_id === this.selectedUser.id) : []);
      return list.filter(l => l && !l.returned_at);
    },

    selectedUserFavoriteBook() {
      if (this.selectedUserFavoriteBookData && this.selectedUserFavoriteBookData.id) {
        return this.selectedUserFavoriteBookData;
      }
      return null;
    },
  },
};
