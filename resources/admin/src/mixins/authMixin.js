export default {
  methods: {
    resetFiltersState() {
      this.searchQuery = '';
      this.sortKey = 'recent';
      this.authorFilterId = '';
      this.booksPage = 1;
      this.booksPerPage = 5;
      this.itemsPerPage = 5;

      this.authorsSearchQuery = '';
      this.authorsSortKey = 'name';
      this.authorsPage = 1;
      this.authorsPerPage = 5;

      this.selectedBook = null;
      this.selectedAuthor = null;

      this.bookReviews = [];
      this.bookReviewsLoading = false;

      this.adminBooksMode = 'active';
      this.adminAuthorsMode = 'active';
      this.deletedBooksSortKey = 'recent';
      this.deletedAuthorsSortKey = 'recent';
    },

    async handleLogin() {
      try {
        this.errorMessage = '';
        this.successMessage = '';

        const data = await this.graphql(
          'mutation Login($input: LoginInput!) { login(input: $input) { token user { id name email is_admin photo } } }',
          { input: { email: this.loginForm.email, password: this.loginForm.password } }
        );

        if (data && data.login && data.login.token && data.login.user) {
          this.authToken = data.login.token;
          this.currentUser = data.login.user;
          localStorage.setItem('authToken', this.authToken);
          localStorage.setItem('currentUser', JSON.stringify(this.currentUser));

          if (typeof this.resetFiltersState === 'function') {
            this.resetFiltersState();
          }

          this.successMessage = this.$t('messages.loginSuccess');
          this.errorMessage = '';
          this.loginForm.email = '';
          this.loginForm.password = '';
          this.routePage = 'books';
          if (typeof window !== 'undefined') {
            window.location.hash = 'books';
          }
          await this.loadBooks();
          await this.loadAuthors();
          await this.loadCart();
          await this.loadUserLoans();
          await this.loadFavoriteBook();
        } else {
          this.errorMessage = this.$t('errors.invalidCredentials');
          this.successMessage = '';
        }
      } catch (e) {
        this.errorMessage = `${this.$t('errors.serverError')} ${e && e.message ? e.message : ''}`.trim();
        this.successMessage = '';
      }
    },

    async handleRegister() {
      try {
        this.errorMessage = '';
        this.successMessage = '';

        if (this.registerForm.password !== this.registerForm.password_confirmation) {
          this.errorMessage = this.$t('errors.passwordMismatch');
          return;
        }

        const data = await this.graphql(
          'mutation Register($input: RegisterInput!) { register(input: $input) { message } }',
          {
            input: {
              name: this.registerForm.name,
              email: this.registerForm.email,
              password: this.registerForm.password,
            },
          }
        );

        if (data && data.register) {
          this.successMessage = this.$t('messages.registerSuccess');
          this.errorMessage = '';
          this.registerForm.name = '';
          this.registerForm.email = '';
          this.registerForm.password = '';
          this.registerForm.password_confirmation = '';
          this.showRegister = false;
          this.routePage = 'login';
          if (typeof window !== 'undefined') {
            window.location.hash = 'login';
          }
        } else {
          this.errorMessage = this.$t('errors.serverError');
          this.successMessage = '';
        }
      } catch (e) {
        this.errorMessage = `${this.$t('errors.serverError')} ${e && e.message ? e.message : ''}`.trim();
        this.successMessage = '';
      }
    },

    async handleRequestPasswordReset() {
      try {
        this.errorMessage = '';
        this.successMessage = '';

        if (!this.forgotEmail) {
          this.errorMessage = this.$t('errors.emailRequired');
          return;
        }

        const data = await this.graphql(
          'mutation RequestPasswordReset($email: String!) { requestPasswordReset(email: $email) { message } }',
          { email: this.forgotEmail }
        );

        if (data && data.requestPasswordReset) {
          this.successMessage = this.$t('messages.passwordResetEmailSent');
          this.errorMessage = '';
          this.forgotEmail = '';
        } else {
          this.errorMessage = this.$t('errors.serverError');
          this.successMessage = '';
        }
      } catch (e) {
        this.errorMessage = `${this.$t('errors.serverError')} ${e && e.message ? e.message : ''}`.trim();
        this.successMessage = '';
      }
    },

    async handleResetPassword() {
      try {
        this.errorMessage = '';
        this.successMessage = '';

        if (!this.resetToken || !this.resetNewPassword || !this.resetNewPasswordConfirm) {
          this.errorMessage = this.$t('forms.required');
          return;
        }

        if (this.resetNewPassword !== this.resetNewPasswordConfirm) {
          this.errorMessage = this.$t('errors.passwordMismatch');
          return;
        }

        const data = await this.graphql(
          'mutation ResetPassword($input: ResetPasswordInput!) { resetPassword(input: $input) { message } }',
          {
            input: {
              email: this.resetEmail,
              token: this.resetToken,
              password: this.resetNewPassword,
            },
          }
        );

        if (data && data.resetPassword) {
          this.successMessage = this.$t('messages.passwordResetSuccess');
          this.errorMessage = '';
          this.closeResetForm();
          this.routePage = 'login';
          if (typeof window !== 'undefined') {
            window.location.hash = 'login';
          }
        } else {
          this.errorMessage = this.$t('errors.invalidToken');
          this.successMessage = '';
        }
      } catch (e) {
        this.errorMessage = `${this.$t('errors.serverError')} ${e && e.message ? e.message : ''}`.trim();
        this.successMessage = '';
      }
    },

    openResetRequest() {
      this.showRegister = false;
      this.showResetForm = true;
      this.errorMessage = '';
      this.successMessage = '';
      this.forgotEmail = this.loginForm.email || '';
    },

    closeResetForm() {
      this.showResetForm = false;
      this.resetToken = '';
      this.resetEmail = '';
      this.resetNewPassword = '';
      this.resetNewPasswordConfirm = '';
    },

    logout() {
      this.authToken = '';
      this.currentUser = null;
      this.cart = [];
      this.userLoans = [];
      this.userFavoriteBook = null;
      this.activeBookIds = [];
      this.routePage = 'login';

      if (typeof this.resetFiltersState === 'function') {
        this.resetFiltersState();
      }

      this.successMessage = '';
      this.errorMessage = '';
      this.showDropdown = false;
      if (typeof window !== 'undefined') {
        localStorage.removeItem('authToken');
        localStorage.removeItem('currentUser');
        window.location.hash = 'login';
      }
    },
  },
};
