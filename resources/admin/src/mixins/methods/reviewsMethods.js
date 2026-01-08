export default {
  methods: {
    async loadBookReviews(bookId) {
      try {
        const id = bookId != null ? String(bookId) : '';
        if (!id) {
          this.bookReviews = [];
          this.bookReviewsLoading = false;
          return;
        }

        this.bookReviewsLoading = true;
        const data = await this.graphql(
          'query ReviewsByBook($bookId: ID!) { reviewsByBook(book_id: $bookId) { id user_id book_id rating comment user { id name photo } } }',
          { bookId: id }
        );
        this.bookReviews = data && Array.isArray(data.reviewsByBook) ? data.reviewsByBook : [];
      } catch (e) {
        this.bookReviews = [];
      } finally {
        this.bookReviewsLoading = false;
      }
    },

    async submitReview(payload) {
      try {
        if (!payload || !payload.book_id) return;
        if (!this.authToken || !this.currentUser) {
          this.errorMessage = this.$t('errors.unauthorized');
          return;
        }

        const rating = Number(payload.rating);
        if (!rating || isNaN(rating) || rating < 1 || rating > 5) {
          this.errorMessage = this.$t('errors.serverError');
          return;
        }

        this.errorMessage = '';
        const data = await this.graphql(
          'mutation UpsertReview($input: ReviewInput!) { upsertReview(input: $input) { id user_id book_id rating comment user { id name photo } } }',
          {
            input: {
              book_id: payload.book_id,
              rating,
              comment: payload.comment != null ? this.safeTrim(String(payload.comment)) : null,
            },
          }
        );

        if (data && data.upsertReview) {
          this.successMessage = this.$t('messages.reviewSaved');
        }

        await this.loadBookReviews(payload.book_id);
      } catch (e) {
        this.setMutationError('reviewSave', e);
      }
    },

    requestDeleteReview(review) {
      if (!review || !review.id) return;
      const message = this.$t('reviews.confirmDelete');
      this.askDelete('review', message, async () => {
        await this.deleteReview(review.id, review.book_id);
      });
    },

    async deleteReview(reviewId, bookId) {
      try {
        if (!reviewId) return;
        await this.graphql(
          'mutation DeleteReview($id: ID!) { deleteReview(id: $id) { message } }',
          { id: reviewId }
        );
        this.successMessage = this.$t('messages.reviewDeleted');
        await this.loadBookReviews(bookId || (this.selectedBook && this.selectedBook.id ? this.selectedBook.id : null));
      } catch (e) {
        this.setMutationError('reviewDelete', e);
      }
    },
  },
};
