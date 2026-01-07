export default {
  computed: {
    totalPages() {
      if (this.booksPageInfo && typeof this.booksPageInfo.lastPage === 'number') {
        return this.booksPageInfo.lastPage || 1;
      }
      return 1;
    },

    filteredBooks() {
      return Array.isArray(this.books) ? this.books : [];
    },

    paginatedBooks() {
      return this.filteredBooks;
    },

    filteredAuthors() {
      return Array.isArray(this.authorsPageData) ? this.authorsPageData : [];
    },

    totalAuthorsPages() {
      if (this.authorsPageInfo && typeof this.authorsPageInfo.lastPage === 'number') {
        return this.authorsPageInfo.lastPage || 1;
      }
      return 1;
    },

    paginatedAuthors() {
      return this.filteredAuthors;
    },
  },
};
