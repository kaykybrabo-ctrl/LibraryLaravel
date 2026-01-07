<template>
  <div id="app" v-cloak @click="handleAppClick" :class="{ 'is-home': routePage === 'home' }">
    <header v-if="currentUser && routePage !== 'home'">
      <div class="header-container">
        <div class="brand" @click="goHome">
          <span class="logo"></span>
          <h1>PedBook</h1>
        </div>
        <div class="user-info">
          <span>{{ $t('common.hello') }}, {{ currentUser.name }}!</span>
          <div class="user-avatar" @click.stop="toggleDropdown">
            <img
              class="avatar"
              :src="thumb(profileFormPhoto || currentUser.photo || '', 100, 100, 'user')"
              alt="Avatar"
              style="width: 100%; height: 100%; object-fit: cover;"
            >
            <div class="user-menu-dropdown" v-if="showDropdown" @click.stop>
              <button class="dropdown-item" @click="goToPage('books'); showDropdown = false">
                {{ $t('navigation.books') }}
              </button>
              <button class="dropdown-item" @click="goToPage('authors'); showDropdown = false">
                {{ $t('navigation.authors') }}
              </button>
              <button
                v-if="currentUser && currentUser.is_admin"
                class="dropdown-item"
                @click="goToPage('users'); showDropdown = false"
              >
                {{ $t('navigation.users') }}
              </button>
              <button
                v-if="currentUser && !currentUser.is_admin"
                class="dropdown-item"
                @click="goToPage('my-loans'); showDropdown = false"
              >
                {{ $t('navigation.loans') }}
              </button>
              <button
                v-if="currentUser && !currentUser.is_admin"
                class="dropdown-item"
                @click="goToPage('cart'); showDropdown = false"
              >
                {{ $t('navigation.cart') }} ({{ cartCount }})
              </button>
              <button
                v-if="currentUser && currentUser.is_admin"
                class="dropdown-item"
                @click="goToPage('loans'); showDropdown = false"
              >
                {{ $t('navigation.loans') }}
              </button>
              <button
                v-if="currentUser && currentUser.is_admin"
                class="dropdown-item"
                @click="goToPage('sales'); showDropdown = false"
              >
                {{ $t('navigation.sales') }}
              </button>
              <button class="dropdown-item" @click="goToPage('profile'); showDropdown = false">
                {{ $t('navigation.profile') }}
              </button>
              <button class="dropdown-logout" @click="logout">
                {{ $t('auth.logout') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </header>

    <header v-else-if="routePage !== 'home'">
      <div class="header-container">
        <div class="brand" @click="goHome">
          <span class="logo"></span>
          <h1>PedBook</h1>
        </div>
        <div style="display:flex; gap:10px;">
          <button class="btn btn-small" @click="goToPage('books')">{{ $t('navigation.books') }}</button>
          <button class="btn btn-small" @click="goToPage('authors')">{{ $t('navigation.authors') }}</button>
          <button class="btn btn-small" @click="goToPage('login')">{{ $t('auth.login') }}</button>
        </div>
      </div>
    </header>

    <div v-if="errorMessage" class="toast toast-error">{{ errorMessage }}</div>
    <div v-if="successMessage" class="toast toast-success">{{ successMessage }}</div>

    <div v-if="routePage === 'login'" class="login-page">
      <div class="login-box">
        <div v-if="errorMessage" class="error">{{ errorMessage }}</div>

        <login-form
          v-if="!showRegister && !showResetForm"
          :loginEmail.sync="loginEmail"
          :loginPassword.sync="loginPassword"
          :loginPasswordVisible.sync="loginPasswordVisible"
          @submit="handleLogin"
          @openReset="openResetRequest"
          @showRegister="showRegister = true"
          @goToLanding="goToLanding"
        />

        <reset-password-form
          v-else-if="showResetForm"
          :resetToken="resetToken"
          :resetEmail="resetEmail"
          :forgotEmail.sync="forgotEmail"
          :resetNewPassword.sync="resetNewPassword"
          :resetNewPasswordConfirm.sync="resetNewPasswordConfirm"
          :resetPasswordVisible.sync="resetPasswordVisible"
          :resetPasswordConfirmVisible.sync="resetPasswordConfirmVisible"
          @requestReset="handleRequestPasswordReset"
          @submitNewPassword="handleResetPassword"
          @backToLogin="closeResetForm"
          @goToLanding="goToLanding"
        />

        <register-form
          v-else
          :register-name.sync="registerForm.name"
          :register-email.sync="registerForm.email"
          :register-password.sync="registerForm.password"
          :register-password-confirmation.sync="registerForm.password_confirmation"
          :register-password-visible.sync="registerPasswordVisible"
          @submit="handleRegister"
          @showLogin="showRegister = false"
          @goToLanding="goToLanding"
        />
      </div>
    </div>

    <main v-else>
      <home-page
        v-if="routePage === 'home'"
      />

      <books-page
        v-else-if="routePage === 'books'"
        :currentUser="currentUser"
        :authors="authors"
        :loading="loading"
        :adminBooksMode="adminBooksMode"
        :deletedBooks="deletedBooks"
        :deletedBooksLoading="deletedBooksLoading"
        :deletedBooksSortKey.sync="deletedBooksSortKey"
        :searchQuery.sync="searchQuery"
        :authorFilterId.sync="authorFilterId"
        :sortKey.sync="sortKey"
        :booksPerPage.sync="booksPerPage"
        :filteredBooksLength="booksPageInfo && typeof booksPageInfo.total === 'number' ? booksPageInfo.total : filteredBooks.length"
        :paginatedBooks="paginatedBooks"
        :totalPages="totalPages"
        :booksPage="booksPage"
        :thumb="thumb"
        :getBookPrice="getBookPrice"
        :isFavorite="isFavorite"
        :isBookUnavailable="isBookUnavailable"
        :isBookBorrowedByMe="isBookBorrowedByMe"
        @openCreateBookModal="openCreateBookModal"
        @viewBook="viewBook"
        @toggleFavorite="toggleFavorite"
        @select-author="selectAuthor"
        @rentOrReturn="handleBookRentOrReturn"
        @switchAdminBooksMode="switchAdminBooksMode"
        @restoreDeletedBook="askRestoreBook"
        @addToCart="addToCart"
        @goLogin="goToPage('login')"
        @openEditBook="openEditBook"
        @askDeleteBook="askDeleteAdmin('book', $event)"
        @changePage="changePage"
      />

      <book-detail-page
        v-else-if="routePage === 'book-detail' && selectedBook"
        :selectedBook="selectedBook"
        :currentUser="currentUser"

        :reviews="bookReviews"
        :reviewsLoading="bookReviewsLoading"

        :thumb="thumb"
        :getBookPrice="getBookPrice"
        :isBookBorrowedByMe="isBookBorrowedByMe"
        :isBookUnavailable="isBookUnavailable"
        :isFavorite="isFavorite"
        @goBack="goToPage('books')"
        @openEditBook="openEditBook"
        @select-author="selectAuthor"
        @borrow="() => openRentModal(selectedBook)"
        @promptReturn="(bookId) => handleBookRentOrReturn({ id: bookId })"
        @toggleFavorite="toggleFavorite(selectedBook)"
        @addToCart="() => addToCart(selectedBook)"
        @goLogin="goToPage('login')"
        @submitReview="submitReview"
        @requestDeleteReview="requestDeleteReview"
      />

      <cart-page
        v-else-if="routePage === 'cart' && currentUser && !currentUser.is_admin"
        :cart-items="cart"
        :cartTotalFormatted="cartTotalFormatted"
        :getBookPrice="getBookPrice"
        :thumb="thumb"
        @goBack="goToPage('books')"
        @viewBook="viewBook"
        @changeQty="changeCartQuantity"
        @remove="removeFromCart"
        @clear="clearCart"
        @checkout="handleCheckout"
      />

      <authors-page
        v-else-if="routePage === 'authors'"
        :currentUser="currentUser"
        :authorsLoading="authorsLoading"

        :adminAuthorsMode="adminAuthorsMode"
        :deletedAuthors="deletedAuthors"
        :deletedAuthorsLoading="deletedAuthorsLoading"
        :deletedAuthorsSortKey.sync="deletedAuthorsSortKey"

        :authorsSearchQuery.sync="authorsSearchQuery"
        :authorsSortKey.sync="authorsSortKey"
        :authorsPerPage.sync="authorsPerPage"
        :paginatedAuthors="paginatedAuthors"
        :totalAuthorsPages="totalAuthorsPages"
        :authorsPage="authorsPage"
        :thumb="thumb"
        @openCreateAuthor="showCreateAuthorModal = true"
        @select-author="selectAuthor"
        @openEditAuthor="openEditAuthor"
        @askDeleteAuthor="askDeleteAdmin('author', $event)"
        @switchAdminAuthorsMode="switchAdminAuthorsMode"
        @restoreDeletedAuthor="askRestoreAuthor"
        @changeAuthorsPage="changeAuthorsPage"
      />

      <author-detail-page
        v-else-if="routePage === 'author-detail' && selectedAuthor"
        :selectedAuthor="selectedAuthor"
        :currentUser="currentUser"
        :thumb="thumb"
        :isBookUnavailable="isBookUnavailable"
        @goBack="goToPage('authors')"
        @openEditAuthor="openEditAuthor"
        @viewBook="viewBook"
      />

      <users-page
        v-else-if="routePage === 'users' && currentUser && currentUser.is_admin"
        :users-loading="usersLoading"
        :paginated-users="paginatedUsers"
        :total-users-pages="totalUsersPages"
        :users-page="usersPage"
        :thumb="thumb"
        @change-users-page="changeUsersPage"
        @open-user-profile="openUserProfile"
      />

      <user-detail-page
        v-else-if="routePage === 'user-detail' && currentUser && currentUser.is_admin && selectedUser"
        :selected-user="selectedUser"
        :selected-user-active-loans="selectedUserActiveLoans"
        :selected-user-favorite-book="selectedUserFavoriteBookData"
        :thumb="thumb"
        :format-date="formatDate"
        @goBack="goToPage('users')"
        @selectAuthor="selectAuthor"
      />

      <loans-page
        v-else-if="routePage === 'loans' && currentUser && currentUser.is_admin"
        :all-loans-loading="allLoansLoading"
        :admin-loans-filter="adminLoansFilter"
        :filtered-admin-loans="filteredAdminLoans"
        :format-date="formatDate"
        @update:admin-loans-filter="adminLoansFilterSafe = $event"
        @return-book="promptReturnAdminLoan"
        @delete-loan="askDeleteAdmin('loan', $event)"
        @open-user-profile="openUserProfile"
      />

      <sales-page
        v-else-if="routePage === 'sales' && currentUser && currentUser.is_admin"
        :admin-orders-loading="adminOrdersLoading"
        :admin-orders="adminOrders"
        :format-date="formatDate"
        :format-order-status="formatOrderStatus"
      />

      <my-loans-page
        v-else-if="routePage === 'my-loans' && currentUser && !currentUser.is_admin"
        :user-loans-filter="userLoansFilter"
        :filtered-user-loans="filteredUserLoans"
        :thumb="thumb"
        :format-date="formatDate"
        :is-loan-overdue="isLoanOverdue"
        @update:user-loans-filter="userLoansFilterSafe = $event"
        @goBack="goToPage('books')"
        @viewBook="viewBook"
        @confirm-return-my-loan="requestReturnMyLoan"
      />

      <profile-page
        v-else-if="routePage === 'profile' && currentUser"
        :current-user="currentUser"
        :editing-profile="editingProfile"
        :profile-form-name="profileFormName"
        :profile-form-photo="profileFormPhoto"
        :user-favorite-book="userFavoriteBook"
        :thumb="thumb"
        @toggle-editing="editingProfile = !editingProfile"
        @update:profile-form-name="profileFormNameSafe = $event"
        @update:profile-form-photo="profileFormPhotoSafe = $event"
        @upload-profile-file="uploadProfileFile"
        @save-profile="saveProfileLocal"
        @removeFavorite="removeFavorite"
        @select-author="selectAuthor"
        @view-book="viewBook"
      />

      <div
        v-else
        class="container"
        style="padding-top: 40px; padding-bottom: 40px;"
      >
        <h2 style="color:#162c74; margin-bottom:10px;">{{ $t('home.loggedAreaTitle') }}</h2>
        <p style="color:#555; max-width:640px;">{{ $t('home.loggedAreaDescription') }}</p>
      </div>
    </main>

    <pix-modal
      :show="showPixModal"
      :pixCode="pixCode"
      :pixAmountFormatted="pixAmountFormatted"
      @close="closePixModal"
      @confirm="confirmPixPayment"
    />

    <rent-modal
      :show="showRentModal"
      :rentTargetBook="rentTargetBook"
      :rentReturnDate.sync="rentReturnDate"
      :minEndDate="minEndDate"
      :maxDate2030="maxDate2030"
      @close="closeRentModal"
      @confirm="confirmRent"
    />

    <create-book-modal
      :show="showCreateBookModal"
      :authors="authors"
      :newBook="newBook"
      :newAuthor="newAuthor"
      :newBookError="newBookError"
      :newBookAuthorMode.sync="newBookAuthorMode"
      @close="closeCreateBookModal"
      @submit="createBook"
      @upload="openUpload"
      @clearError="newBookError = ''"
    />

    <edit-book-modal
      :show="showEditBookModal"
      :authors="authors"
      :editBook="editBook"
      @close="showEditBookModal = false"
      @submit="saveEditBook"
      @upload="openUpload"
    />

    <create-author-modal
      :show="showCreateAuthorModal"
      :newAuthor="newAuthor"
      @close="showCreateAuthorModal = false"
      @submit="createAuthor"
      @upload="openUpload"
    />

    <edit-author-modal
      :show="showEditAuthorModal"
      :editAuthor="editAuthor"
      @close="showEditAuthorModal = false"
      @submit="saveEditAuthor"
      @upload="openUpload"
    />

    <confirm-modal
      :show="showConfirmModal"
      :title="confirmTitle || $t('common.confirmActionTitle')"
      :message="confirmMessage"
      :confirmLabel="confirmConfirmLabel"
      :cancelLabel="confirmCancelLabel"
      :isDanger="confirmIsDanger"
      @close="closeConfirmModal"
      @confirm="confirmModalProceed"
    />

    <upload-modal
      :show="showUploadModal"
      @close="closeUploadModal"
      @uploaded="handleUploadSuccess"
    />
  </div>
</template>

<script>
import LoginForm from './Auth/LoginForm.vue';
import RegisterForm from './Auth/RegisterForm.vue';
import ResetPasswordForm from './Auth/ResetPasswordForm.vue';
import CartPage from './Cart/CartPage.vue';
import BooksPage from './Books/BooksPage.vue';
import BookDetailPage from './Books/BookDetailPage.vue';
import AuthorsPage from './Authors/AuthorsPage.vue';
import AuthorDetailPage from './Authors/AuthorDetailPage.vue';
import PixModal from './Modals/PixModal.vue';
import RentModal from './Modals/RentModal.vue';
import CreateBookModal from './Modals/CreateBookModal.vue';
import EditBookModal from './Modals/EditBookModal.vue';
import CreateAuthorModal from './Modals/CreateAuthorModal.vue';
import EditAuthorModal from './Modals/EditAuthorModal.vue';
import ConfirmModal from './Modals/ConfirmModal.vue';
import UsersPage from './Users/UsersPage.vue';
import UserDetailPage from './Users/UserDetailPage.vue';
import LoansPage from './Loans/LoansPage.vue';
import SalesPage from './Sales/SalesPage.vue';
import MyLoansPage from './Loans/MyLoansPage.vue';
import ProfilePage from './Profile/ProfilePage.vue';
import UploadModal from './Modals/UploadModal.vue';
import HomePage from './Home/HomePage.vue';
import graphqlMixin from '../mixins/graphqlMixin';
import utilsMixin from '../mixins/utilsMixin';
import dataMixin from '../mixins/combined/dataMixin';
import computedMixin from '../mixins/combined/computedMixin';
import authMixin from '../mixins/authMixin';
import loadersMixin from '../mixins/loadersMixin';
import booksMixin from '../mixins/combined/booksMixin';
import adminMixin from '../mixins/adminMixin';
import uiMixin from '../mixins/uiMixin';
import lifecycleMixin from '../mixins/lifecycleMixin';

const isBrowser = typeof window !== 'undefined';

export default {
  name: 'App',

  components: {
    HomePage,
    LoginForm,
    RegisterForm,
    ResetPasswordForm,
    CartPage,
    BooksPage,
    BookDetailPage,
    AuthorsPage,
    AuthorDetailPage,
    PixModal,
    RentModal,
    CreateBookModal,
    EditBookModal,
    CreateAuthorModal,
    EditAuthorModal,
    ConfirmModal,
    UsersPage,
    UserDetailPage,
    LoansPage,
    SalesPage,
    MyLoansPage,
    ProfilePage,
    UploadModal,
  },

  mixins: [graphqlMixin, utilsMixin, dataMixin, computedMixin, authMixin, loadersMixin, booksMixin, adminMixin, uiMixin, lifecycleMixin],

  data() {
    return {
      editBook: {
        id: '',
        title: '',
        author_id: '',
        description: '',
        photo: '',
        price: null,
      },
      editAuthor: {
        id: '',
        name: '',
        bio: '',
        photo: '',
      },
    };
  },

  computed: {
    cartCount() {
      return Array.isArray(this.cart) ? this.cart.length : 0;
    },
    cartItems() {
      return Array.isArray(this.cart) ? this.cart : [];
    },
    userLoansFilterSafe: {
      get() {
        return typeof this.userLoansFilter === 'string' && this.userLoansFilter ? this.userLoansFilter : 'all';
      },
      set(v) {
        const next = typeof v === 'string' && v ? v : 'all';
        if (this.userLoansFilter == null) {
          this.$set(this, 'userLoansFilter', next);
        } else {
          this.userLoansFilter = next;
        }
      },
    },
    adminLoansFilterSafe: {
      get() {
        return typeof this.adminLoansFilter === 'string' && this.adminLoansFilter ? this.adminLoansFilter : 'all';
      },
      set(v) {
        const next = typeof v === 'string' && v ? v : 'all';
        if (this.adminLoansFilter == null) {
          this.$set(this, 'adminLoansFilter', next);
        } else {
          this.adminLoansFilter = next;
        }
      },
    },
    profileFormNameSafe: {
      get() {
        return typeof this.profileFormName === 'string' ? this.profileFormName : '';
      },
      set(v) {
        const next = typeof v === 'string' ? v : '';
        if (this.profileFormName == null) {
          this.$set(this, 'profileFormName', next);
        } else {
          this.profileFormName = next;
        }
      },
    },
    profileFormPhotoSafe: {
      get() {
        return typeof this.profileFormPhoto === 'string' ? this.profileFormPhoto : '';
      },
      set(v) {
        const next = typeof v === 'string' ? v : '';
        if (this.profileFormPhoto == null) {
          this.$set(this, 'profileFormPhoto', next);
        } else {
          this.profileFormPhoto = next;
        }
      },
    },
    loginEmail: {
      get() {
        return this.loginForm && typeof this.loginForm.email === 'string' ? this.loginForm.email : '';
      },
      set(v) {
        if (!this.loginForm) this.$set(this, 'loginForm', { email: '', password: '' });
        this.$set(this.loginForm, 'email', v || '');
      },
    },

    loginPassword: {
      get() {
        return this.loginForm && typeof this.loginForm.password === 'string' ? this.loginForm.password : '';
      },
      set(v) {
        if (!this.loginForm) this.$set(this, 'loginForm', { email: '', password: '' });
        this.$set(this.loginForm, 'password', v || '');
      },
    },
  },

  watch: {
    booksPerPage() {
      this.booksPage = 1;
      if (this.routePage === 'books') {
        this.loadBooks();
      }
    },
    authorsPerPage() {
      this.authorsPage = 1;
      if (this.routePage === 'authors' && !(this.currentUser && this.currentUser.is_admin && this.adminAuthorsMode === 'deleted')) {
        this.loadAuthorsPage();
      }
    },
    successMessage(val) {
      if (val && typeof val === 'string' && val.trim() && isBrowser) {
        setTimeout(() => {
          if (this.successMessage === val) {
            this.successMessage = '';
          }
        }, 4000);
      }
    },
  },

  methods: {
    handleAppClick() {
      this.showDropdown = false;
    },

    goHome() {
      if (!this.currentUser) {
        this.routePage = 'home';
        if (isBrowser) window.location.hash = 'home';
        return;
      }

      this.routePage = 'books';
      if (isBrowser) window.location.hash = 'books';
    },

    openCreateBookModal() {
      this.newBookError = '';
      this.errorMessage = '';
      this.showCreateBookModal = true;
    },

    closeCreateBookModal() {
      this.showCreateBookModal = false;
      this.newBookError = '';
    },

    async createBook() {
      try {
        this.newBookError = '';

        const payload = {
          title: this.newBook.title,
          description: this.newBook.description || '',
          photo: this.newBook.photo || '',
          price: this.newBook.price != null ? Number(this.newBook.price) : null,
        };

        if (this.newBookAuthorMode === 'existing') {
          if (!this.newBook.author_id) {
            this.newBookError = this.$t('errors.selectAuthorForBook');
            return;
          }
          payload.author_id = parseInt(this.newBook.author_id, 10);
        } else {
          if (!this.newAuthor.name) {
            this.newBookError = this.$t('errors.enterNewAuthorName');
            return;
          }
          payload.author_name = this.newAuthor.name;
          payload.author_bio = this.newAuthor.bio || '';
          payload.author_photo = this.newAuthor.photo || '';
        }

        await this.graphql(
          'mutation CreateBook($input: CreateBookInput!) { createBook(input: $input) { id } }',
          { input: payload },
        );

        await this.loadBooks();
        await this.loadAuthors();

        this.showCreateBookModal = false;
        this.newBook = { title: '', author_id: '', description: '', photo: '', price: null };
        this.newBookAuthorMode = 'existing';
        this.newAuthor = { name: '', bio: '', photo: '' };
        this.newBookError = '';
        this.errorMessage = '';
        this.successMessage = this.$t('messages.bookCreatedSuccess');
      } catch (e) {
        this.errorMessage = this.$t('errors.bookCreateFailed');
      }
    },

    openEditBook(book) {
      this.editBook = {
        id: book.id,
        title: book.title || '',
        author_id: book.author && book.author.id ? book.author.id : '',
        description: book.description || '',
        photo: book.photo || '',
        price: typeof book.price === 'number' && book.price > 0 ? book.price : this.getBookPrice(book),
      };
      this.showEditBookModal = true;
    },

    async saveEditBook() {
      try {
        const payload = {
          title: this.editBook.title,
          description: this.editBook.description,
          author_id: parseInt(this.editBook.author_id, 10),
          photo: this.editBook.photo,
          price: this.editBook.price != null ? Number(this.editBook.price) : null,
        };

        const data = await this.graphql(
          'mutation UpdateBook($id: ID!, $input: UpdateBookInput!) { updateBook(id: $id, input: $input) { id title description photo price author { id name bio photo } } }',
          { id: this.editBook.id, input: payload },
        );
        this.showEditBookModal = false;
        await this.loadBooks();
        if (this.selectedBook && String(this.selectedBook.id) === String(this.editBook.id)) {
          this.selectedBook = data && data.updateBook ? data.updateBook : this.selectedBook;
        }
      } catch (e) {
        this.errorMessage = this.$t('errors.bookUpdateFailed');
      }
    },

    async deleteBook(id) {
      try {
        await this.graphql(
          'mutation DeleteBook($id: ID!) { deleteBook(id: $id) { message } }',
          { id },
        );
        await this.loadBooks();
        this.successMessage = this.$t('messages.bookDeletedSuccess');
        this.errorMessage = '';
      } catch (e) {
        this.errorMessage = this.$t('errors.bookDeleteFailed');
      }
    },

    askDeleteAdmin(type, id) {
      const doDelete = () => {
        if (type === 'book') return this.deleteBook(id);
        if (type === 'author') return this.deleteAuthor(id);
        if (type === 'loan') return this.deleteLoan(id);
        return undefined;
      };

      this.askDelete(type, this.$t('admin.confirmDeletePrompt'), doDelete);

      return undefined;
    },

    async saveProfileLocal() {
      try {
        if (!this.currentUser) return;
        this.errorMessage = '';
        this.successMessage = '';
        const payload = {
          name: this.profileFormName,
          photo: this.profileFormPhoto || (this.currentUser && this.currentUser.photo) || '',
        };
        const data = await this.graphql(
          'mutation UpdateProfile($input: UpdateProfileInput!) { updateProfile(input: $input) { id name email is_admin photo } }',
          { input: payload },
        );
        const updated = data && data.updateProfile ? data.updateProfile : this.currentUser;
        this.currentUser = updated;
        if (isBrowser) {
          localStorage.setItem('currentUser', JSON.stringify(updated));
        }
        this.editingProfile = false;
        this.successMessage = this.$t('messages.profileUpdated');
      } catch (e) {
        const msg = e && e.message ? String(e.message) : '';
        if (msg.toLowerCase().includes('unauthenticated')) {
          this.logout();
          return;
        }
        this.errorMessage = `${this.$t('errors.profileUpdateFailed')} ${msg}`.trim();
      }
    },

    async uploadProfileFile(evt) {
      try {
        const file = evt && evt.target && evt.target.files ? evt.target.files[0] : null;
        if (!file) return;
        if (!file.type || !String(file.type).startsWith('image/')) {
          this.errorMessage = this.$t('errors.selectImageFile');
          return;
        }
        if (file.size > 5 * 1024 * 1024) {
          this.errorMessage = this.$t('errors.maxFileSize', { max: 5 });
          return;
        }
        this.errorMessage = '';
        this.successMessage = '';

        const fileData = await new Promise((resolve, reject) => {
          const reader = new FileReader();
          reader.onload = () => resolve(String(reader.result || ''));
          reader.onerror = () => reject(new Error(this.$t('errors.fileReadFailed')));
          reader.readAsDataURL(file);
        });

        const data = await this.graphql(
          'mutation UploadImage($target: String!, $filename: String!, $fileData: String!) { uploadImage(target: $target, filename: $filename, fileData: $fileData) }',
          {
            target: 'profile',
            filename: file.name || 'profile',
            fileData,
          }
        );

        const publicId = data && data.uploadImage ? data.uploadImage : '';
        if (!publicId) {
          this.errorMessage = this.$t('errors.uploadFailed');
          return;
        }

        this.uploadTarget = 'profile';
        this.handleUploadSuccess(publicId);

        if (evt && evt.target) evt.target.value = '';
        this.successMessage = this.$t('messages.imageUploaded');
      } catch (e) {
        const msg = e && e.message ? String(e.message) : '';
        if (msg.toLowerCase().includes('unauthenticated')) {
          this.logout();
          return;
        }
        this.errorMessage = `${this.$t('errors.imageUploadFailed')} ${msg}`.trim();
      }
    },

    requestReturnMyLoan(loanId) {
      if (!loanId) return;
      this.askDelete('loan', this.$t('loans.confirmReturn'), () => this.confirmReturnMyLoan(loanId));
    },
  },

  async mounted() {
    if (!isBrowser) return;

    try {
      const storedUser = localStorage.getItem('currentUser');
      if (storedUser) {
        this.currentUser = JSON.parse(storedUser);
      }
    } catch (e) {}

    const storedToken = isBrowser ? localStorage.getItem('authToken') : null;
    if (storedToken) {
      this.authToken = storedToken;
    }

    const path = window.location.pathname;

    if (path === '/login') {
      this.routePage = 'login';
    } else if (path === '/register') {
      this.routePage = 'login';
      this.showRegister = true;
    } else if (path === '/reset-password') {
      this.routePage = 'login';
      this.showRegister = false;
      this.showResetForm = true;

      try {
        const params = new URLSearchParams(window.location.search || '');
        this.resetToken = params.get('token') || '';
        this.resetEmail = params.get('email') || '';
      } catch (e) {

      }
    } else {
      this.routePage = path === '/' ? 'home' : 'books';
    }

    await this.loadBooks();
    await this.loadAuthors();

    if (this.currentUser) {
      await this.loadUserData();
    }

    const hash = window.location.hash ? window.location.hash.substring(1) : '';
    if (hash) {
      await this.handleRouteHash(hash);
    }

    window.addEventListener('hashchange', () => this.handleRouteHash());
  },
};
</script>

<style scoped>
  [v-cloak] {
    display: none;
  }
</style>
