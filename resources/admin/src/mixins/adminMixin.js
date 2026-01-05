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

    openCreateAuthorModal() {
      this.newAuthor = {
        name: '',
        bio: '',
        photo: '',
      };
      this.showCreateAuthorModal = true;
    },

    closeCreateAuthorModal() {
      this.showCreateAuthorModal = false;
      this.newAuthor = {
        name: '',
        bio: '',
        photo: '',
      };
    },

    async createAuthor() {
      try {
        if (!this.newAuthor.name) return;
        await this.graphql(
          'mutation CreateAuthor($name: String!, $bio: String, $photo: String) { createAuthor(name: $name, bio: $bio, photo: $photo) { id } }',
          this.newAuthor
        );
        await this.loadAuthors();
        this.closeCreateAuthorModal();
      } catch (e) {

      }
    },

    openEditAuthor(author) {
      this.editAuthor = {
        id: author.id,
        name: author.name,
        bio: author.bio || '',
        photo: author.photo || '',
      };
      this.showEditAuthorModal = true;
    },

    closeEditAuthorModal() {
      this.showEditAuthorModal = false;
      this.editAuthor = null;
    },

    async saveEditAuthor() {
      try {
        if (!this.editAuthor || !this.editAuthor.name) return;
        await this.graphql(
          'mutation UpdateAuthor($id: ID!, $name: String!, $bio: String, $photo: String) { updateAuthor(id: $id, name: $name, bio: $bio, photo: $photo) { id } }',
          this.editAuthor
        );
        await this.loadAuthors();
        await this.loadBooks();
        this.closeEditAuthorModal();
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

    async deleteAuthor(authorId) {
      try {
        await this.graphql(
          'mutation DeleteAuthor($id: ID!) { deleteAuthor(id: $id) }',
          { id: authorId }
        );
        await this.loadAuthors();
        await this.loadBooks();
      } catch (e) {

      }
    },

    async deleteLoan(loanId) {
      try {
        await this.graphql(
          'mutation DeleteLoan($id: ID!) { deleteLoan(id: $id) { message } }',
          { id: loanId }
        );
        await this.loadAllLoans();
      } catch (e) {

      }
    },

    promptReturnAdminLoan(loanId) {
      const id = Number(loanId);
      if (!id) return;
      this.confirmTitle = 'Devolver Livro';
      this.confirmMessage = 'Deseja devolver este livro?';
      this.confirmConfirmLabel = 'Devolver';
      this.confirmCancelLabel = 'Cancelar';
      this.confirmIsDanger = false;
      this.confirmCallback = () => this.returnBookAdminLoan(id);
      this.showConfirmModal = true;
    },

    async returnBookAdminLoan(loanId) {
      try {
        this.errorMessage = '';
        this.successMessage = '';
        await this.graphql(
          'mutation ReturnBook($id: ID!) { returnBook(id: $id) { id } }',
          { id: loanId }
        );
        await this.loadAllLoans();
        this.successMessage = '✅ Livro devolvido com sucesso.';
      } catch (e) {
        this.errorMessage = '❌ Não foi possível devolver o livro.';
      }
    },

    openUserProfile(user) {
      this.selectedUser = user;
      this.selectedUserLoans = [];
      this.selectedUserFavoriteBookData = null;
      this.routePage = 'user-detail';
      if (typeof window !== 'undefined') {
        window.location.hash = `user/${user.id}`;
      }

      this.graphql(
        'query UserLoans($userId: ID!) { userLoans(user_id: $userId) { id loan_date return_date returned_at status is_overdue days_remaining book { id title author { id name } } } }',
        { userId: user.id }
      )
        .then((data) => {
          const list = data && Array.isArray(data.userLoans) ? data.userLoans : [];
          this.selectedUserLoans = list;
        })
        .catch(() => {});

      this.graphql(
        'query FavoriteByUser($userId: ID!) { favoriteBookByUser(user_id: $userId) { id title description photo author { id name } } }',
        { userId: user.id }
      )
        .then((data) => {
          const book = data && data.favoriteBookByUser ? data.favoriteBookByUser : null;
          this.selectedUserFavoriteBookData = book && book.id ? book : null;
        })
        .catch(() => {});
    },

    changeUsersPage(page) {
      if (page >= 1 && page <= this.totalUsersPages) {
        this.usersPage = page;
        if (typeof window !== 'undefined') {
          window.scrollTo(0, 0);
        }
      }
    },
  },
};
