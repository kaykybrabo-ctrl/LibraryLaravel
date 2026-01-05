export default {
  methods: {
    async toggleFavorite(book) {
      if (!book || !this.currentUser) return;
      try {
        await this.graphql(
          'mutation ToggleFavorite($input: ToggleFavoriteInput!) { toggleFavorite(input: $input) { id } }',
          { input: { user_id: this.currentUser.id, book_id: book.id } }
        );
        await this.loadFavoriteBook();
      } catch (e) {

      }
    },

    async removeFavorite() {
      if (!this.userFavoriteBook) return;
      try {
        await this.graphql(
          'mutation RemoveFavorite { removeFavorite }'
        );
        this.userFavoriteBook = null;
      } catch (e) {

      }
    },
  },
};
