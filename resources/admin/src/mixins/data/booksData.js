export default {
  data() {
    return {
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

      adminBooksMode: 'active',
      deletedBooks: [],
      deletedBooksLoading: false,
      
      searchQuery: '',
      authorFilterId: '',
      sortKey: 'recent',
      
      userLoans: [],
      userFavoriteBook: null,
      activeBookIds: [],
      
      cart: [],
      cartOpen: false,
    };
  },
};
