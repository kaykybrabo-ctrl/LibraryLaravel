export default {
  data() {
    return {
      currentUser: null,
      authToken: '',
      showDropdown: false,
      routePage: 'login',
      successMessage: '',
      errorMessage: '',
      
      books: [],
      loading: false,
      booksPage: 1,
      booksPerPage: 5,
      itemsPerPage: 5,
      
      authors: [],
      authorsLoading: false,
      authorsPage: 1,
      authorsPerPage: 5,
      authorsSearchQuery: '',
      
      selectedAuthor: null,
      selectedBook: null,
      borrowStartDate: '',
      borrowEndDate: '',
      
      searchQuery: '',
      authorFilterId: '',
      sortKey: 'recent',
      
      userLoans: [],
      userFavoriteBook: null,
      activeBookIds: [],
      
      cart: [],
      cartOpen: false,
      
      showPixModal: false,
      pixCode: '',
      
      showRentModal: false,
      rentTargetBook: null,
      rentReturnDate: '',
      
      showCreateBookModal: false,
      showEditBookModal: false,
      editBook: null,
      newBookAuthorMode: 'existing',
      newBookError: '',
      newBook: {
        title: '',
        author_id: '',
        description: '',
        photo: '',
        price: null,
      },
      newAuthor: {
        name: '',
        bio: '',
        photo: '',
      },
      showCreateAuthorModal: false,
      showEditAuthorModal: false,
      editAuthor: null,
      
      showConfirmModal: false,
      confirmTitle: '',
      confirmMessage: '',
      confirmConfirmLabel: '',
      confirmCancelLabel: '',
      confirmIsDanger: false,
      confirmCallback: null,
      
      showUploadModal: false,
      uploadTarget: null,
      
      users: [],
      usersLoading: false,
      usersPage: 1,
      usersPerPage: 10,
      totalUsers: 0,
      selectedUser: null,
      
      
      allLoans: [],
      allLoansLoading: false,
      adminLoansFilter: 'all',
      
      adminOrders: [],
      adminOrdersLoading: false,
      
      userLoansFilter: 'all',
      
      editingProfile: false,
      profileFormName: '',
      profileFormPhoto: '',
      
      showRegister: false,
      showResetForm: false,
      resetEmail: '',
      resetCode: '',
      resetToken: '',
      forgotEmail: '',
      resetNewPassword: '',
      resetNewPasswordConfirm: '',
      resetPasswordVisible: false,
      resetPasswordConfirmVisible: false,
      
      loginForm: {
        email: '',
        password: '',
      },
      loginPasswordVisible: false,
      registerForm: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
      },
    };
  },
};
