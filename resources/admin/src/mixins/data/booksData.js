export default {
  data() {
    return {
      books: [],
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
      
      adminBooksMode: 'active',
      deletedBooks: [],
      deletedBooksSortKey: 'recent',

      adminAuthorsMode: 'active',
      deletedAuthors: [],
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
