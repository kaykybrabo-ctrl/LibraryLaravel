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
      this.confirmTitle = 'Restaurar Livro';
      this.confirmMessage = 'Deseja restaurar este livro?';
      this.confirmConfirmLabel = 'Restaurar';
      this.confirmCancelLabel = 'Cancelar';
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
          this.newBookError = 'Título e autor são obrigatórios.';
          return;
        }
        const variables = { ...this.newBook };
        if (this.newBookAuthorMode === 'new') {
          variables.new_author_name = this.newBook.author_id;
          variables.author_id = null;
        }
        await this.graphql(
          'mutation CreateBook($title: String!, $authorId: ID, $newAuthorName: String, $description: String, $photo: String, $price: Float) { createBook(title: $title, author_id: $authorId, new_author_name: $newAuthorName, description: $description, photo: $photo, price: $price) { id } }',
          variables
        );
        await this.loadBooks();
        await this.loadAuthors();
        this.closeCreateBookModal();
      } catch (e) {
        this.newBookError = 'Erro ao criar livro.';
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
          'mutation UpdateBook($id: ID!, $title: String!, $authorId: ID!, $description: String, $photo: String, $price: Float) { updateBook(id: $id, title: $title, author_id: $authorId, description: $description, photo: $photo, price: $price) { id } }',
          {
            id: this.editBook.id,
            title: this.editBook.title,
            authorId: this.editBook.author_id,
            description: this.editBook.description,
            photo: this.editBook.photo,
            price: this.editBook.price,
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
          'mutation DeleteBook($id: ID!) { deleteBook(id: $id) }',
          { id: bookId }
        );
        await this.loadBooks();
      } catch (e) {

      }
    },
  },
};
