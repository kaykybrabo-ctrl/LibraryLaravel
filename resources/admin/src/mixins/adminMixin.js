export default {
  methods: {
    async switchAdminBooksMode(mode) {
      this.adminBooksMode = mode === 'deleted' ? 'deleted' : 'active';
      if (this.adminBooksMode === 'deleted') {
        await this.loadDeletedBooks();
      }
    },

    async switchAdminAuthorsMode(mode) {
      this.adminAuthorsMode = mode === 'deleted' ? 'deleted' : 'active';
      if (this.adminAuthorsMode === 'deleted') {
        await this.loadDeletedAuthors();
      } else {
        if (typeof this.loadAuthorsPage === 'function') {
          await this.loadAuthorsPage();
        }
      }
    },

    async loadDeletedBooks() {
      if (!this.currentUser || !this.currentUser.is_admin) return;
      try {
        this.deletedBooksLoading = true;
        const data = await this.graphql(
          'query DeletedBooks($sort: String) { deletedBooks(sort: $sort) { id title description photo price author { id name } } }',
          { sort: this.deletedBooksSortKey || 'recent' }
        );
        this.deletedBooks = (data && data.deletedBooks) ? data.deletedBooks : [];
      } catch (e) {
        this.deletedBooks = [];
      } finally {
        this.deletedBooksLoading = false;
      }
    },

    async loadDeletedAuthors() {
      if (!this.currentUser || !this.currentUser.is_admin) return;
      try {
        this.deletedAuthorsLoading = true;
        const data = await this.graphql(
          'query DeletedAuthors($sort: String) { deletedAuthors(sort: $sort) { id name bio photo books { id } } }',
          { sort: this.deletedAuthorsSortKey || 'recent' }
        );
        this.deletedAuthors = (data && data.deletedAuthors) ? data.deletedAuthors : [];
      } catch (e) {
        this.deletedAuthors = [];
      } finally {
        this.deletedAuthorsLoading = false;
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

    askRestoreAuthor(authorId) {
      const id = Number(authorId);
      if (!id) return;
      this.confirmTitle = `${this.$t('common.restore')} ${this.$t('entities.author')}`;
      this.confirmMessage = this.$t('common.confirmRestoreAuthor');
      this.confirmConfirmLabel = this.$t('common.restore');
      this.confirmCancelLabel = this.$t('common.cancel');
      this.confirmIsDanger = false;
      this.confirmCallback = () => this.restoreAuthorAdmin(id);
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

    async restoreAuthorAdmin(authorId) {
      try {
        await this.graphql(
          'mutation RestoreAuthor($id: ID!) { restoreAuthor(id: $id) { id } }',
          { id: authorId }
        );
        await this.loadAuthors();
        if (typeof this.loadAuthorsPage === 'function') {
          await this.loadAuthorsPage();
        }
        await this.loadBooks();
        if (this.adminAuthorsMode === 'deleted') {
          await this.loadDeletedAuthors();
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
          'mutation CreateAuthor($input: CreateAuthorInput!) { createAuthor(input: $input) { id } }',
          { input: this.newAuthor }
        );
        await this.loadAuthors();
        if (typeof this.loadAuthorsPage === 'function') {
          await this.loadAuthorsPage();
        }
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
          'mutation UpdateAuthor($id: ID!, $input: UpdateAuthorInput!) { updateAuthor(id: $id, input: $input) { id } }',
          {
            id: this.editAuthor.id,
            input: {
              name: this.editAuthor.name,
              bio: this.editAuthor.bio,
              photo: this.editAuthor.photo,
            },
          }
        );
        await this.loadAuthors();
        if (typeof this.loadAuthorsPage === 'function') {
          await this.loadAuthorsPage();
        }
        await this.loadBooks();
        this.closeEditAuthorModal();
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

    async deleteAuthor(authorId) {
      try {
        await this.graphql(
          'mutation DeleteAuthor($id: ID!) { deleteAuthor(id: $id) { message } }',
          { id: authorId }
        );
        await this.loadAuthors();
        await this.loadBooks();
        if (typeof this.loadAuthorsPage === 'function') {
          await this.loadAuthorsPage();
        }
        if (this.adminAuthorsMode === 'deleted') {
          await this.loadDeletedAuthors();
        }
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
      this.confirmTitle = `${this.$t('books.return')} ${this.$t('entities.book')}`;
      this.confirmMessage = this.$t('loans.confirmReturn');
      this.confirmConfirmLabel = this.$t('books.return');
      this.confirmCancelLabel = this.$t('common.cancel');
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
        this.successMessage = this.$t('messages.bookReturned');
      } catch (e) {
        this.errorMessage = this.$t('errors.returnFailed');
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
