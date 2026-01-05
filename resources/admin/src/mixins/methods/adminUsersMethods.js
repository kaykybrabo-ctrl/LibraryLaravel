export default {
  methods: {
    openUserProfile(user) {
      this.selectedUser = user;
      this.selectedUserLoans = [];
      this.selectedUserFavoriteBook = null;
      this.routePage = 'user-detail';
      if (typeof window !== 'undefined') {
        window.location.hash = `user/${user.id}`;
      }

      this.graphql(
        'query UserLoans($userId: ID!) { userLoans(user_id: $userId) { id loan_date return_date returned_at status is_overdue days_remaining book { id title author { id name } } } }',
        { userId: user.id }
      )
        .then((data) => {
          const list = data && Array.isArray(data.userLoans) ? data.userLoans : [];
          this.selectedUserLoans = list;
        })
        .catch(() => {});

      this.graphql(
        'query FavoriteByUser($userId: ID!) { favoriteBookByUser(user_id: $userId) { id title description photo author { id name } } }',
        { userId: user.id }
      )
        .then((data) => {
          const book = data && data.favoriteBookByUser ? data.favoriteBookByUser : null;
          this.selectedUserFavoriteBook = book && book.id ? book : null;
        })
        .catch(() => {});
    },

    changeUsersPage(page) {
      if (page >= 1 && page <= this.totalUsersPages) {
        this.usersPage = page;
        if (typeof window !== 'undefined') {
          window.scrollTo(0, 0);
        }
      }
    },

    async deleteLoan(loanId) {
      try {
        await this.graphql(
          'mutation DeleteLoan($id: ID!) { deleteLoan(id: $id) { message } }',
          { id: loanId }
        );
        await this.loadAllLoans();
      } catch (e) {

      }
    },
  },
};
