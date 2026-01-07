export default {
  data() {
    return {
      books: [],
      loading: false,
      booksPage: 1,
      booksPerPage: 5,
      itemsPerPage: 5,

      booksPageInfo: {
        total: 0,
        perPage: 5,
        currentPage: 1,
        lastPage: 1,
        hasMorePages: false,
        count: 0,
      },
      
      authors: [],
      authorsLoading: false,
      authorsPage: 1,
      authorsPerPage: 5,
      authorsSearchQuery: '',
      authorsSortKey: 'name',

      authorsPageData: [],
      authorsPageInfo: {
        total: 0,
        perPage: 5,
        currentPage: 1,
        lastPage: 1,
        hasMorePages: false,
        count: 0,
      },
      
      selectedAuthor: null,
      selectedBook: null,
      borrowStartDate: '',
      borrowEndDate: '',

      bookReviews: [],
      bookReviewsLoading: false,

      adminBooksMode: 'active',
      deletedBooks: [],
      deletedBooksLoading: false,
      deletedBooksSortKey: 'recent',

      adminAuthorsMode: 'active',
      deletedAuthors: [],
      deletedAuthorsLoading: false,
      deletedAuthorsSortKey: 'recent',
      
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
