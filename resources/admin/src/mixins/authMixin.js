export default {
  methods: {
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
          this.successMessage = '✅ Login realizado com sucesso!';
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
          this.errorMessage = '❌ Credenciais inválidas.';
          this.successMessage = '';
        }
      } catch (e) {
        this.errorMessage = '❌ Erro ao fazer login: ' + (e.message || 'Erro desconhecido');
        this.successMessage = '';
      }
    },

    async handleRegister() {
      try {
        this.errorMessage = '';
        this.successMessage = '';

        if (this.registerForm.password !== this.registerForm.password_confirmation) {
          this.errorMessage = '❌ Senhas não conferem.';
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
          this.successMessage = '✅ Registro realizado com sucesso! Faça login para continuar.';
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
          this.errorMessage = '❌ Erro ao registrar. Tente novamente.';
          this.successMessage = '';
        }
      } catch (e) {
        this.errorMessage = '❌ Erro ao registrar: ' + (e.message || 'Erro desconhecido');
        this.successMessage = '';
      }
    },

    async handleRequestPasswordReset() {
      try {
        this.errorMessage = '';
        this.successMessage = '';

        if (!this.forgotEmail) {
          this.errorMessage = '❌ Email é obrigatório.';
          return;
        }

        const data = await this.graphql(
          'mutation RequestPasswordReset($email: String!) { requestPasswordReset(email: $email) { message } }',
          { email: this.forgotEmail }
        );

        if (data && data.requestPasswordReset) {
          this.successMessage = '✅ Solicitação enviada! Verifique o link gerado nos logs do backend (modo dev).';
          this.errorMessage = '';
          this.forgotEmail = '';
        } else {
          this.errorMessage = '❌ Erro ao solicitar recuperação. Verifique o email.';
          this.successMessage = '';
        }
      } catch (e) {
        this.errorMessage = '❌ Erro: ' + (e.message || 'Erro desconhecido');
        this.successMessage = '';
      }
    },

    async handleResetPassword() {
      try {
        this.errorMessage = '';
        this.successMessage = '';

        if (!this.resetToken || !this.resetNewPassword || !this.resetNewPasswordConfirm) {
          this.errorMessage = '❌ Preencha todos os campos.';
          return;
        }

        if (this.resetNewPassword !== this.resetNewPasswordConfirm) {
          this.errorMessage = '❌ Senhas não conferem.';
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
          this.successMessage = '✅ Senha redefinida com sucesso! Faça login.';
          this.errorMessage = '';
          this.closeResetForm();
          this.routePage = 'login';
          if (typeof window !== 'undefined') {
            window.location.hash = 'login';
          }
        } else {
          this.errorMessage = '❌ Token inválido ou expirado.';
          this.successMessage = '';
        }
      } catch (e) {
        this.errorMessage = '❌ Erro ao redefinir senha: ' + (e.message || 'Erro desconhecido');
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
