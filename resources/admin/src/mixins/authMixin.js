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
      if (typeof window !== 'undefined' && window.$uiStore) {
        window.$uiStore.setLoading('bookReviews', false);
      }

      this.adminBooksMode = 'active';
      this.adminAuthorsMode = 'active';
      this.deletedBooksSortKey = 'recent';
      this.deletedAuthorsSortKey = 'recent';
    },

    resetAuthUiState() {
      this.errorMessage = '';
      this.successMessage = '';
      this.authFieldErrors = {};
    },

    extractAuthFieldErrors(e) {
      const errors =
        e &&
        (e.validationErrors ||
          (e.meta && e.meta.errors) ||
          (e.extensions && e.extensions.meta && e.extensions.meta.errors));

      if (errors && typeof errors === 'object') {
        return errors;
      }

      return {};
    },

    getAuthErrorMessage(e) {
      const msg = e && e.message ? String(e.message).trim() : '';
      const code = e && e.code ? String(e.code) : '';

      if (code === 'invalid_credentials') return this.$t('errors.invalidCredentials');
      if (code === 'invalid_token') return this.$t('errors.invalidToken');
      if (code === 'user_not_found') return this.$t('errors.userNotFound');

      if (msg) return msg;
      return this.$t('errors.serverError');
    },

    handleAuthError(e) {
      const code = e && e.code ? String(e.code) : '';

      if (code === 'invalid_credentials') {
        const msg = this.$t
          ? this.$t('errors.invalidCredentials')
          : (e && e.message) || 'Credenciais inválidas.';

        this.authFieldErrors = { email: [msg] };
        this.errorMessage = msg;
        this.successMessage = '';
        return;
      }

      this.authFieldErrors = this.extractAuthFieldErrors(e);
      this.errorMessage = this.getAuthErrorMessage(e);
      this.successMessage = '';
    },

    async handleLogin() {
      if (typeof window !== 'undefined' && window.$uiStore) {
        window.$uiStore.setLoading('auth', true);
      }
      try {
        this.resetAuthUiState();

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
          await this.loadActiveBookIds();
          await this.loadFavoriteBook();
        } else {
          this.handleAuthError({ code: 'invalid_credentials' });
        }
      } catch (e) {
        this.handleAuthError(e);
      } finally {
        if (typeof window !== 'undefined' && window.$uiStore) {
          window.$uiStore.setLoading('auth', false);
        }
      }
    },

    async handleRegister() {
      if (typeof window !== 'undefined' && window.$uiStore) {
        window.$uiStore.setLoading('auth', true);
      }
      try {
        this.resetAuthUiState();

        if (this.registerForm.password !== this.registerForm.password_confirmation) {
          this.errorMessage = this.$t('errors.passwordMismatch');
          this.authFieldErrors = { password_confirmation: [this.errorMessage] };
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
          this.handleAuthError({});
        }
      } catch (e) {
        this.handleAuthError(e);
      } finally {
        if (typeof window !== 'undefined' && window.$uiStore) {
          window.$uiStore.setLoading('auth', false);
        }
      }
    },

    async handleRequestPasswordReset() {
      if (typeof window !== 'undefined' && window.$uiStore) {
        window.$uiStore.setLoading('auth', true);
      }
      try {
        this.resetAuthUiState();

        if (!this.forgotEmail) {
          this.errorMessage = this.$t('errors.emailRequired');
          this.authFieldErrors = { email: [this.errorMessage] };
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
          this.handleAuthError({});
        }
      } catch (e) {
        this.handleAuthError(e);
      } finally {
        if (typeof window !== 'undefined' && window.$uiStore) {
          window.$uiStore.setLoading('auth', false);
        }
      }
    },

    async handleResetPassword() {
      if (typeof window !== 'undefined' && window.$uiStore) {
        window.$uiStore.setLoading('auth', true);
      }
      try {
        this.resetAuthUiState();

        if (!this.resetToken || !this.resetNewPassword || !this.resetNewPasswordConfirm) {
          this.errorMessage = this.$t('forms.required');
          this.authFieldErrors = {
            password: !this.resetNewPassword ? [this.errorMessage] : undefined,
            password_confirmation: !this.resetNewPasswordConfirm ? [this.errorMessage] : undefined,
          };
          return;
        }

        if (this.resetNewPassword !== this.resetNewPasswordConfirm) {
          this.errorMessage = this.$t('errors.passwordMismatch');
          this.authFieldErrors = { password_confirmation: [this.errorMessage] };
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
          this.handleAuthError({ code: 'invalid_token' });
        }
      } catch (e) {
        this.handleAuthError(e);
      } finally {
        if (typeof window !== 'undefined' && window.$uiStore) {
          window.$uiStore.setLoading('auth', false);
        }
      }
    },

    openResetRequest() {
      this.showRegister = false;
      this.showResetForm = true;
      this.resetAuthUiState();
      this.forgotEmail = this.loginForm.email || '';
    },

    closeResetForm() {
      this.showResetForm = false;
      this.resetToken = '';
      this.resetEmail = '';
      this.resetNewPassword = '';
      this.resetNewPasswordConfirm = '';
      this.errorMessage = '';
      this.authFieldErrors = {};
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
