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

    getBookPrice(book) {
      try {
        if (!book) return 0;
        const raw = book.price;
        if (raw == null) return 0;
        if (typeof raw === 'number' && !isNaN(raw)) return raw;
        const parsed = Number(raw);
        return isNaN(parsed) ? 0 : parsed;
      } catch (e) {
        return 0;
      }
    },

    async selectAuthor(author) {
      this.selectedAuthor = author;
      this.routePage = 'author-detail';
      if (typeof window !== 'undefined') {
        window.location.hash = `author/${author.id}`;
      }
    },

    viewBook(book) {
      this.selectedBook = book;
      this.routePage = 'book-detail';
      if (typeof window !== 'undefined') {
        window.location.hash = `book/${book.id}`;
      }

      if (typeof this.loadBookReviews === 'function') {
        this.loadBookReviews(book && book.id ? book.id : null);
      }
    },
  },
};
