export default {
  methods: {
    async loadBooks() {
      try {
        this.loading = true;
        const data = await this.graphql(
          'query Books($all: Boolean) { books(all: $all) { id title description photo price created_at author { id name bio photo } } }',
          { all: true },
        );
        this.books = data && Array.isArray(data.books) ? data.books : [];
      } catch (e) {
        this.books = [];
      } finally {
        this.loading = false;
      }
    },

    async loadAuthors() {
      try {
        this.authorsLoading = true;
        const data = await this.graphql(
          'query Authors { authors { id name bio photo } }',
        );
        this.authors = data && Array.isArray(data.authors) ? data.authors : [];
      } catch (e) {
        this.authors = [];
      } finally {
        this.authorsLoading = false;
      }
    },

    async loadCart() {
      try {
        if (!this.authToken || !this.currentUser) {
          this.cart = [];
          return;
        }
        const data = await this.graphql('query MyCart { myCart { id quantity book { id title photo price } } }');
        this.cart = data && Array.isArray(data.myCart) ? data.myCart : [];
      } catch (e) {
        this.cart = [];
      }
    },

    async loadUserLoans() {
      try {
        if (!this.authToken || !this.currentUser || !this.currentUser.id) {
          this.userLoans = [];
          this.activeBookIds = [];
          return;
        }

        const data = await this.graphql(
          'query UserLoans($user_id: ID!) { userLoans(user_id: $user_id) { id book_id loan_date return_date returned_at status is_overdue days_remaining book { id title photo author { id name } } } }',
          { user_id: this.currentUser.id },
        );
        this.userLoans = data && Array.isArray(data.userLoans) ? data.userLoans : [];
        this.activeBookIds = this.userLoans
          .filter(l => !l.returned_at)
          .map(l => (l.book_id != null ? l.book_id : (l.book && l.book.id ? l.book.id : null)))
          .filter(Boolean);
      } catch (e) {
        this.userLoans = [];
        this.activeBookIds = [];
      }
    },

    async loadFavoriteBook() {
      try {
        if (!this.authToken || !this.currentUser || !this.currentUser.id) {
          this.userFavoriteBook = null;
          return;
        }

        const data = await this.graphql(
          'query FavoriteBookByUser($user_id: ID!) { favoriteBookByUser(user_id: $user_id) { id title description photo author { id name } } }',
          { user_id: this.currentUser.id },
        );
        this.userFavoriteBook = data && data.favoriteBookByUser ? data.favoriteBookByUser : null;
      } catch (e) {
        this.userFavoriteBook = null;
      }
    },

    async loadUsers() {
      try {
        this.usersLoading = true;
        const data = await this.graphql(
          'query Users($per: Int) { users(per_page: $per) { id name email is_admin photo } }',
          { per: 100 },
        );
        this.users = data && Array.isArray(data.users) ? data.users : [];
      } catch (e) {
        this.users = [];
      } finally {
        this.usersLoading = false;
      }
    },

    async loadAllLoans() {
      try {
        this.allLoansLoading = true;
        const data = await this.graphql(
          'query Loans { loans { id user_id book_id loan_date return_date returned_at status is_overdue days_remaining user { id name email is_admin photo } book { id title author { id name } } } }',
        );
        this.allLoans = data && Array.isArray(data.loans) ? data.loans : [];
      } catch (e) {
        this.allLoans = [];
      } finally {
        this.allLoansLoading = false;
      }
    },

    async loadAdminOrders() {
      try {
        this.adminOrdersLoading = true;
        const data = await this.graphql(
          'query Orders { orders { id user_id total status created_at user { id name email } items { id book_id quantity unit_price book { id title author { id name } } } } }',
        );
        this.adminOrders = data && Array.isArray(data.orders) ? data.orders : [];
      } catch (e) {
        this.adminOrders = [];
      } finally {
        this.adminOrdersLoading = false;
      }
    },

    async loadUserData() {
      if (!this.currentUser) return;
      try {
        await Promise.all([
          this.loadCart(),
          this.loadUserLoans(),
          this.loadFavoriteBook(),
        ]);
      } catch (e) {

      }
    },
  },
};
