export default {
  methods: {
    async switchAdminBooksMode(mode) {
      this.adminBooksMode = mode === 'deleted' ? 'deleted' : 'active';
      if (this.adminBooksMode === 'deleted') {
        await this.loadDeletedBooks();
      }
    },

    async loadDeletedBooks() {
      if (!this.currentUser || !this.currentUser.is_admin) return;
      try {
        this.deletedBooksLoading = true;
        const data = await this.graphql(
          'query DeletedBooks { deletedBooks { id title description photo price author { id name } } }'
        );
        this.deletedBooks = (data && data.deletedBooks) ? data.deletedBooks : [];
      } catch (e) {
        this.deletedBooks = [];
      } finally {
        this.deletedBooksLoading = false;
      }
    },

    askRestoreBook(bookId) {
      const id = Number(bookId);
      if (!id) return;
      this.confirmTitle = `${this.$t('common.restore')} ${this.$t('entities.book')}`;
      this.confirmMessage = this.$t('common.confirmRestoreBook');
      this.confirmConfirmLabel = this.$t('common.restore');
      this.confirmCancelLabel = this.$t('common.cancel');
      this.confirmIsDanger = false;
      this.confirmCallback = () => this.restoreBookAdmin(id);
      this.showConfirmModal = true;
    },

    async restoreBookAdmin(bookId) {
      try {
        await this.graphql(
          'mutation RestoreBook($id: ID!) { restoreBook(id: $id) { id } }',
          { id: bookId }
        );
        await this.loadBooks();
        await this.loadAuthors();
        if (this.adminBooksMode === 'deleted') {
          await this.loadDeletedBooks();
        }
      } catch (e) {
      }
    },

    openCreateBookModal() {
      this.newBook = {
        title: '',
        author_id: '',
        description: '',
        photo: '',
        price: null,
      };
      this.newBookAuthorMode = 'existing';
      this.newBookError = '';
      this.showCreateBookModal = true;
    },

    closeCreateBookModal() {
      this.showCreateBookModal = false;
      this.newBook = {
        title: '',
        author_id: '',
        description: '',
        photo: '',
        price: null,
      };
      this.newBookAuthorMode = 'existing';
      this.newBookError = '';
    },

    async createBook() {
      try {
        this.newBookError = '';
        if (!this.newBook.title || !this.newBook.author_id) {
          this.newBookError = this.$t('errors.titleAndAuthorRequired');
          return;
        }
        const input = {
          title: this.newBook.title,
          description: this.newBook.description,
          photo: this.newBook.photo,
          price: this.newBook.price,
          author_id: this.newBook.author_id,
        };
        if (this.newBookAuthorMode === 'new') {
          input.author_name = this.newBook.author_id;
          input.author_id = null;
        }
        await this.graphql(
          'mutation CreateBook($input: CreateBookInput!) { createBook(input: $input) { id } }',
          { input }
        );
        await this.loadBooks();
        await this.loadAuthors();
        this.closeCreateBookModal();
      } catch (e) {
        this.newBookError = this.$t('errors.bookCreateError');
      }
    },

    openEditBook(book) {
      this.editBook = {
        id: book.id,
        title: book.title,
        author_id: book.author ? book.author.id : '',
        description: book.description || '',
        photo: book.photo || '',
        price: book.price,
      };
      this.showEditBookModal = true;
    },

    closeEditBookModal() {
      this.showEditBookModal = false;
      this.editBook = null;
    },

    async saveEditBook() {
      try {
        if (!this.editBook || !this.editBook.title || !this.editBook.author_id) return;
        await this.graphql(
          'mutation UpdateBook($id: ID!, $input: UpdateBookInput!) { updateBook(id: $id, input: $input) { id } }',
          {
            id: this.editBook.id,
            input: {
              title: this.editBook.title,
              author_id: this.editBook.author_id,
              description: this.editBook.description,
              photo: this.editBook.photo,
              price: this.editBook.price,
            },
          }
        );
        await this.loadBooks();
        await this.loadAuthors();
        this.closeEditBookModal();
      } catch (e) {

      }
    },

    async deleteBook(bookId) {
      try {
        await this.graphql(
          'mutation DeleteBook($id: ID!) { deleteBook(id: $id) { message } }',
          { id: bookId }
        );
        await this.loadBooks();
      } catch (e) {

      }
    },
  },
};
