export default {
  data() {
    return {
      users: [],
      usersLoading: false,
      usersPage: 1,
      usersPerPage: 10,
      totalUsers: 0,
      selectedUser: null,

      selectedUserLoans: [],
      selectedUserFavoriteBookData: null,
      
      allLoans: [],
      allLoansLoading: false,
      adminLoansFilter: 'all',
      
      adminOrders: [],
      adminOrdersLoading: false,
      
      userLoansFilter: 'all',
      
      editingProfile: false,
      profileFormName: '',
      profileFormPhoto: '',
    };
  },
};
