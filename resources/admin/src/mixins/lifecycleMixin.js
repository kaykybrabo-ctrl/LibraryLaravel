export default {
  methods: {
    async handleRouteHash(h) {
      if (typeof window === 'undefined') return;
      const hash = (h || (window.location.hash ? window.location.hash.substring(1) : '')).replace(/^#/, '');
      if (!hash) return;

      const restricted = ['users', 'user-detail', 'profile', 'cart', 'loans', 'sales', 'my-loans'];
      if (!this.currentUser && restricted.includes(hash)) {
        this.routePage = 'login';
        window.location.hash = 'login';
        return;
      }

      if (['books', 'authors', 'author-detail', 'users', 'user-detail', 'profile', 'login', 'cart', 'loans', 'sales', 'my-loans'].includes(hash)) {
        this.routePage = hash;
        if (hash === 'books') {
          if (typeof this.loadAuthors === 'function') {
            this.loadAuthors();
          }
          if (typeof this.loadBooks === 'function') {
            this.loadBooks();
          }
        }
        if (hash === 'authors') {
          if (this.currentUser && this.currentUser.is_admin && this.adminAuthorsMode === 'deleted') {
            if (typeof this.loadDeletedAuthors === 'function') {
              this.loadDeletedAuthors();
            }
          } else {
            if (typeof this.loadAuthorsPage === 'function') {
              this.loadAuthorsPage();
            }
          }
        }
        if (hash === 'users' && this.currentUser && this.currentUser.is_admin) {
          this.loadUsers();
        }
        if (hash === 'loans' && this.currentUser && this.currentUser.is_admin) {
          this.loadAllLoans();
        }
        if (hash === 'sales' && this.currentUser && this.currentUser.is_admin) {
          this.loadAdminOrders();
        }
        if (hash === 'my-loans' && this.currentUser && !this.currentUser.is_admin) {
          this.loadUserData();
        }
        return;
      }

      if (hash.startsWith('user/')) {
        const id = parseInt(hash.split('/')[1], 10);
        if (id && this.currentUser && this.currentUser.is_admin) {
          if (!Array.isArray(this.users) || this.users.length === 0) {
            await this.loadUsers();
          }
          const u = (this.users || []).find((x) => Number(x.id) === Number(id));
          if (u) {
            this.openUserProfile(u);
          }
        }
        return;
      }

      if (hash.startsWith('book/')) {
        const id = parseInt(hash.split('/')[1], 10);
        if (id) {
          try {
            const data = await this.graphql(
              'query Book($id: ID!) { book(id: $id) { id title description photo price author { id name bio photo } } }',
              { id },
            );
            if (data && data.book) {
              await this.viewBook(data.book);
            }
          } catch (e) {
          }
        }
      }

      if (hash.startsWith('author/')) {
        const id = parseInt(hash.split('/')[1], 10);
        if (id) {
          try {
            const data = await this.graphql(
              'query Author($id: ID!) { author(id: $id) { id name bio photo books { id title description photo price author { id name } } } }',
              { id },
            );
            if (data && data.author) {
              await this.selectAuthor(data.author);
            }
          } catch (e) {

          }
        }
      }
    },
  },

  async mounted() {
    if (typeof window !== 'undefined') {
      const token = localStorage.getItem('authToken');
      const user = localStorage.getItem('currentUser');
      if (token && user) {
        try {
          this.authToken = token;
          this.currentUser = JSON.parse(user);
          await Promise.all([
            this.loadBooks(),
            this.loadAuthors(),
            this.loadUserData(),
          ]);
        } catch (e) {
          this.logout();
        }
      }
      this.handleRouteHash();
      window.addEventListener('hashchange', () => this.handleRouteHash());
      window.addEventListener('click', (e) => {
        if (!e.target.closest('.user-dropdown')) {
          this.closeDropdown();
        }
      });
    }
  },

  watch: {
    searchQuery() {
      this.booksPage = 1;
      if (this.routePage === 'books') {
        this.loadBooks();
      }
    },
    authorFilterId() {
      this.booksPage = 1;
      if (this.routePage === 'books') {
        this.loadBooks();
      }
    },
    sortKey() {
      this.booksPage = 1;
      if (this.routePage === 'books') {
        this.loadBooks();
      }
    },
    deletedBooksSortKey() {
      if (this.routePage === 'books' && this.currentUser && this.currentUser.is_admin && this.adminBooksMode === 'deleted') {
        if (typeof this.loadDeletedBooks === 'function') {
          this.loadDeletedBooks();
        }
      }
    },
    authorsSearchQuery() {
      this.authorsPage = 1;
      if (this.routePage === 'authors' && !(this.currentUser && this.currentUser.is_admin && this.adminAuthorsMode === 'deleted')) {
        this.loadAuthorsPage();
      }
    },
    authorsSortKey() {
      this.authorsPage = 1;
      if (this.routePage === 'authors' && !(this.currentUser && this.currentUser.is_admin && this.adminAuthorsMode === 'deleted')) {
        this.loadAuthorsPage();
      }
    },
    deletedAuthorsSortKey() {
      if (this.routePage === 'authors' && this.currentUser && this.currentUser.is_admin && this.adminAuthorsMode === 'deleted') {
        if (typeof this.loadDeletedAuthors === 'function') {
          this.loadDeletedAuthors();
        }
      }
    },
    routePage(val) {
      if (val === 'profile' && this.currentUser) {
        this.profileFormName = this.currentUser.name || '';
        this.profileFormPhoto = this.currentUser.photo || '';
      }
    },
    currentUser(val, oldVal) {
      const oldId = oldVal && oldVal.id != null ? String(oldVal.id) : '';
      const newId = val && val.id != null ? String(val.id) : '';

      if (newId !== oldId) {
        if (typeof this.resetFiltersState === 'function') {
          this.resetFiltersState();
        }
      }

      if (val) {
        this.profileFormName = val.name || '';
        this.profileFormPhoto = val.photo || '';
      }
    },
  },
};
