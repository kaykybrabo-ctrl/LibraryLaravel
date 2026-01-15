export default {
  data() {
    return {
      users: [],
      usersPage: 1,
      usersPerPage: 10,
      totalUsers: 0,
      selectedUser: null,

      selectedUserLoans: [],
      selectedUserFavoriteBookData: null,
      
      allLoans: [],
      adminLoansFilter: 'all',
      
      adminOrders: [],
      
      userLoansFilter: 'all',
      
      editingProfile: false,
      profileFormName: '',
      profileFormPhoto: '',
    };
  },
};
