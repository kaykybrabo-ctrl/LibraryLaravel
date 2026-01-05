export default {
  computed: {
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
  },
};
