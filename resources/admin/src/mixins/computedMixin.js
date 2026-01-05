export default {
  computed: {
    minStartDate() {
      const today = new Date();
      return today.toISOString().split('T')[0];
    },

    totalPages() {
      return Math.ceil(this.filteredBooks.length / this.booksPerPage);
    },

    filteredBooks() {
      let filtered = this.books;
      if (this.searchQuery) {
        const q = this.searchQuery.toLowerCase();
        filtered = filtered.filter(b =>
          (b.title && b.title.toLowerCase().includes(q)) ||
          (b.description && b.description.toLowerCase().includes(q)) ||
          (b.author && b.author.name && b.author.name.toLowerCase().includes(q))
        );
      }
      if (this.authorFilterId) {
        filtered = filtered.filter(b => b.author && b.author.id === this.authorFilterId);
      }
      if (this.sortKey === 'recent') {
        filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      } else if (this.sortKey === 'title') {
        filtered.sort((a, b) => (a.title || '').localeCompare(b.title || ''));
      } else if (this.sortKey === 'author') {
        filtered.sort((a, b) => (a.author && a.author.name || '').localeCompare(b.author && b.author.name || ''));
      }
      return filtered;
    },

    paginatedBooks() {
      const start = (this.booksPage - 1) * this.booksPerPage;
      return this.filteredBooks.slice(start, start + this.booksPerPage);
    },

    filteredAuthors() {
      let filtered = this.authors;
      if (this.authorsSearchQuery) {
        const q = this.authorsSearchQuery.toLowerCase();
        filtered = filtered.filter(a =>
          (a.name && a.name.toLowerCase().includes(q)) ||
          (a.bio && a.bio.toLowerCase().includes(q))
        );
      }
      return filtered;
    },

    totalAuthorsPages() {
      return Math.ceil(this.filteredAuthors.length / this.authorsPerPage);
    },

    paginatedAuthors() {
      const start = (this.authorsPage - 1) * this.authorsPerPage;
      return this.filteredAuthors.slice(start, start + this.authorsPerPage);
    },

    cartTotal() {
      return this.cart.reduce((sum, item) => sum + (item.book ? (item.book.price || 0) * item.quantity : 0), 0);
    },

    cartTotalFormatted() {
      return 'R$ ' + this.cartTotal.toFixed(2).replace('.', ',');
    },

    pixAmountFormatted() {
      return 'R$ ' + this.cartTotal.toFixed(2).replace('.', ',');
    },

    isBookUnavailable() {
      return (book) => {
        if (!book) return true;
        return this.activeBookIds.includes(book.id);
      };
    },

    isBookBorrowedByMe() {
      return (book) => {
        if (!book || !this.userLoans) return false;
        return this.userLoans.some(loan => loan.book_id === book.id && !loan.returned_at);
      };
    },

    isFavorite() {
      return (book) => {
        if (!book || !this.userFavoriteBook) return false;
        return this.userFavoriteBook.id === book.id;
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
        : (Array.isArray(this.allLoans) ? this.allLoans : []);
      return list.filter(l => l && !l.returned_at);
    },

    selectedUserFavoriteBook() {
      if (!this.selectedUser) return null;
      if (this.selectedUser.favorite_book) return this.selectedUser.favorite_book;
      if (this.books && this.books.length) {
        return this.books.find(b => b.id === this.selectedUser.favorite_book_id);
      }
      return null;
    },

    formatOrderStatus() {
      return (status) => {
        const map = {
          pending: 'Pendente',
          paid: 'Pago',
          canceled: 'Cancelado',
        };
        return map[status] || status;
      };
    },

    formatDate() {
      return (dateStr) => {
        if (!dateStr) return '';
        try {
          const d = new Date(dateStr);
          if (isNaN(d.getTime())) return '';
          return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        } catch (e) {
          return '';
        }
      };
    },

    totalUsersPages() {
      return Math.ceil(this.totalUsers / this.usersPerPage);
    },

    paginatedUsers() {
      const start = (this.usersPage - 1) * this.usersPerPage;
      return this.users.slice(start, start + this.usersPerPage);
    },
  },
};
