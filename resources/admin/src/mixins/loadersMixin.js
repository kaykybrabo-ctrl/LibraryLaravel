export default {
  methods: {
    async loadBooks() {
      try {
        this.loading = true;
        const data = await this.graphql(
          'query BooksPage($per: Int!, $page: Int, $search: String, $authorId: ID, $sort: String) { booksPage(per_page: $per, page: $page, search: $search, author_id: $authorId, sort: $sort) { data { id title description photo price created_at author { id name bio photo } } pageInfo { total perPage currentPage lastPage hasMorePages count } } }',
          {
            per: this.booksPerPage || 12,
            page: this.booksPage || 1,
            search: this.searchQuery || null,
            authorId: this.authorFilterId || null,
            sort: this.sortKey || 'recent',
          },
        );
        const page = data && data.booksPage ? data.booksPage : null;
        this.books = page && Array.isArray(page.data) ? page.data : [];
        this.booksPageInfo = page && page.pageInfo ? page.pageInfo : {
          total: 0,
          perPage: this.booksPerPage || 12,
          currentPage: this.booksPage || 1,
          lastPage: 1,
          hasMorePages: false,
          count: 0,
        };
      } catch (e) {
        this.books = [];
        this.booksPageInfo = {
          total: 0,
          perPage: this.booksPerPage || 12,
          currentPage: this.booksPage || 1,
          lastPage: 1,
          hasMorePages: false,
          count: 0,
        };
      } finally {
        this.loading = false;
      }
    },

    async loadAuthors() {
      try {
        this.authorsLoading = true;
        const data = await this.graphql(
          'query Authors { authors { id name } }',
        );
        this.authors = data && Array.isArray(data.authors) ? data.authors : [];
      } catch (e) {
        this.authors = [];
      } finally {
        this.authorsLoading = false;
      }
    },

    async loadAuthorsPage() {
      try {
        this.authorsLoading = true;
        const data = await this.graphql(
          'query AuthorsPage($per: Int!, $page: Int, $search: String, $sort: String) { authorsPage(per_page: $per, page: $page, search: $search, sort: $sort) { data { id name bio photo books { id } } pageInfo { total perPage currentPage lastPage hasMorePages count } } }',
          {
            per: this.authorsPerPage || 12,
            page: this.authorsPage || 1,
            search: this.authorsSearchQuery || null,
            sort: this.authorsSortKey || 'name',
          },
        );
        const page = data && data.authorsPage ? data.authorsPage : null;
        this.authorsPageData = page && Array.isArray(page.data) ? page.data : [];
        this.authorsPageInfo = page && page.pageInfo ? page.pageInfo : {
          total: 0,
          perPage: this.authorsPerPage || 12,
          currentPage: this.authorsPage || 1,
          lastPage: 1,
          hasMorePages: false,
          count: 0,
        };
      } catch (e) {
        this.authorsPageData = [];
        this.authorsPageInfo = {
          total: 0,
          perPage: this.authorsPerPage || 12,
          currentPage: this.authorsPage || 1,
          lastPage: 1,
          hasMorePages: false,
          count: 0,
        };
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
