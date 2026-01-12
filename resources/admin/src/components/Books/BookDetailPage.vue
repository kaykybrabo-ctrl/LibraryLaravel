<template>
  <div class="container">
    <button
      class="btn btn-secondary btn-small"
      style="width: auto; margin-bottom: 15px;"
      @click="$emit('goBack')"
    >
      {{ $t('navigation.back') }}
    </button>

    <div class="detail-container" style="display: grid; grid-template-columns: 300px 1fr; gap: 30px;">
      <div class="detail-image" style="width: 300px; height: 400px; border-radius: 8px; overflow: hidden;">
        <img
          :src="thumb(selectedBook.photo, 600, 800, 'book')"
          :alt="selectedBook.title"
          @error="$event.target.src = thumb('', 600, 800, 'book')"
          style="width:100%;height:100%;object-fit:cover;"
        >
      </div>
      <div class="detail-info">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin:0 0 10px 0;">
          <h2 style="margin:0;">{{ selectedBook.title }}</h2>
          <button
            v-if="currentUser && currentUser.is_admin"
            class="btn btn-small"
            style="width:auto;"
            @click="$emit('openEditBook', selectedBook)"
          >
            {{ $t('common.edit') }}
          </button>
        </div>

        <p style="color:#666; margin: 5px 0 15px 0;">
          {{ $t('books.author') }}:
          <strong
            style="cursor:pointer;color:#162c74;text-decoration:underline;"
            @click="$emit('select-author', selectedBook.author)"
          >
            {{ selectedBook.author && selectedBook.author.name ? selectedBook.author.name : $t('books.unknownAuthor') }}
          </strong>
        </p>

        <p style="color:#666; line-height:1.6;">{{ selectedBook.description || $t('books.noDescription') }}</p>

        <p style="margin-top:10px; font-weight:700; color:#162c74;">
          {{ $formatCurrency(getBookPrice(selectedBook)) }}
        </p>

        <div style="margin-top:16px; background:#f8f9fa; padding:12px; border-radius:8px;">
          <h3 style="margin:0 0 10px 0;">{{ $t('books.rent') }}</h3>

          <div v-if="currentUser && !currentUser.is_admin">
            <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
              <div v-if="isBookBorrowedByMe(selectedBook.id) && selectedBook.return_date" class="form-group" style="margin:0;">
                <label>{{ $t('loans.returnDate') }}</label>
                <div style="height: 38px; display:flex; align-items:center; padding:0 10px; border:1px solid #dee2e6; border-radius:6px; background:#fff; min-width: 140px;">
                  {{ selectedBook.return_date }}
                </div>
              </div>

              <button
                v-if="!isBookBorrowedByMe(selectedBook.id)"
                class="btn btn-small"
                style="width:auto;"
                :disabled="isBookUnavailable(selectedBook.id)"
                @click="$emit('borrow')"
              >
                {{ isBookUnavailable(selectedBook.id) ? $t('books.unavailable') : $t('books.rent') }}
              </button>

              <button
                v-else
                class="btn btn-small btn-danger"
                style="width:auto;"
                @click="$emit('promptReturn', selectedBook.id)"
              >
                {{ $t('books.return') }}
              </button>

              <button class="btn btn-small" style="width:auto;" @click.stop="$emit('toggleFavorite')">
                {{ isFavorite(selectedBook.id) ? $t('books.removeFromFavorites') : $t('books.addToFavorites') }}
              </button>
              <button class="btn btn-small btn-secondary" style="width:auto;" @click.stop="$emit('addToCart')">
                {{ $t('books.addToCart') }}
              </button>
            </div>

            <div v-if="isBookBorrowedByMe(selectedBook.id)" style="margin-top:8px; font-size:0.85rem; color:#16a34a;">
              {{ $t('books.borrowedByMe') }}
            </div>
            <div v-else-if="isBookUnavailable(selectedBook.id)" style="margin-top:8px; font-size:0.85rem; color:#b91c1c;">
              {{ $t('books.borrowedByOther') }}
            </div>
          </div>

          <div v-else>
            <button v-if="!currentUser" class="btn btn-small" @click="$emit('goLogin')">{{ $t('books.loginToRentOrBuy') }}</button>
            <div v-else style="padding:8px 0; color:#555; font-size:0.9rem;">{{ $t('books.adminNoRentFavorite') }}</div>
          </div>
        </div>

        <div style="margin-top:16px; background:#f8f9fa; padding:12px; border-radius:8px;">
          <h3 style="margin:0 0 10px 0;">{{ $t('reviews.title') }}</h3>

          <div v-if="reviewsLoading" class="text-center">
            <LoadingSpinner :text="$t('common.loading')" />
          </div>

          <div v-else>
            <p v-if="!reviews || reviews.length === 0" style="color:#666;">{{ $t('reviews.noReviews') }}</p>

            <div v-else style="display:flex; flex-direction:column; gap:10px;">
              <div
                v-for="r in reviews"
                :key="r.id"
                style="padding:10px; border:1px solid #dee2e6; border-radius:8px; background:#fff;"
              >
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                  <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                    <div style="width:32px; height:32px; border-radius:50%; background:#e5e7eb; overflow:hidden; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-weight:700; color:#162c74;">
                      <img
                        v-if="r.user && r.user.photo"
                        :src="thumb(r.user.photo, 64, 64, 'user')"
                        alt="avatar"
                        style="width:100%; height:100%; object-fit:cover;"
                      >
                      <span v-else aria-hidden="true">{{ r.user && r.user.name ? r.user.name.charAt(0).toUpperCase() : 'U' }}</span>
                    </div>
                    <div style="font-weight:600; color:#162c74; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                      {{ r.user && r.user.name ? r.user.name : '' }}
                    </div>
                  </div>
                  <div style="display:flex; align-items:center; gap:8px;">
                    <div style="display:flex; gap:2px; line-height:1;">
                      <span
                        v-for="n in 5"
                        :key="n"
                        :style="{ fontSize: '16px', color: n <= Number(r.rating) ? '#162c74' : '#cbd5e1' }"
                      >★</span>
                    </div>
                    <div style="font-size:0.85rem; color:#666;">
                      {{ $t('reviews.ratingValue', { n: r.rating }) }}
                    </div>
                  </div>
                </div>
                <div v-if="r.comment" style="margin-top:6px; color:#444; line-height:1.45;">
                  {{ r.comment }}
                </div>
              </div>
            </div>

            <div style="margin-top:14px; border-top:1px solid #e5e7eb; padding-top:12px;">
              <div v-if="!currentUser">
                <button class="btn btn-small" style="width:auto;" @click="$emit('goLogin')">
                  {{ $t('reviews.loginToReview') }}
                </button>
              </div>

              <div v-else-if="currentUser && currentUser.is_admin" style="color:#666; font-size:0.9rem;">
                {{ $t('reviews.adminCannotReview') }}
              </div>

              <div v-else>
                <h4 style="margin:0 0 8px 0; color:#162c74;">{{ $t('reviews.yourReview') }}</h4>

                <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
                  <div class="form-group" style="margin:0; min-width:140px;">
                    <label>{{ $t('reviews.rating') }}</label>
                    <div
                      style="display:flex; gap:4px; line-height:1; background:#fff; border:1px solid #dee2e6; border-radius:6px; height:38px; align-items:center; padding-left:10px; padding-right:10px;"
                      @mouseleave="hoverRating = 0"
                    >
                      <span
                        v-for="n in 5"
                        :key="n"
                        role="button"
                        tabindex="0"
                        :aria-label="$t('reviews.starAria', { n })"
                        :title="$t('reviews.starAria', { n })"
                        @mouseenter="hoverRating = n"
                        @click="reviewRating = n"
                        @keydown.enter.prevent="reviewRating = n"
                        @keydown.space.prevent="reviewRating = n"
                        :style="{ fontSize: '22px', color: n <= activeReviewRating ? '#162c74' : '#cbd5e1', cursor: 'pointer' }"
                      >★</span>
                    </div>

                    <div v-if="reviewRating === 0" style="margin-top:6px; color:#666; font-size:0.85rem;">
                      {{ $t('reviews.clickToRate') }}
                    </div>
                  </div>

                  <div class="form-group" style="margin:0; flex:1; min-width:220px;">
                    <label>{{ $t('reviews.comment') }}</label>
                    <textarea
                      v-model="reviewComment"
                      rows="2"
                      style="width:100%; padding:8px; border:1px solid #dee2e6; border-radius:6px;"
                    ></textarea>
                  </div>
                </div>

                <div style="margin-top:10px; display:flex; gap:8px;">
                  <button
                    class="btn btn-small"
                    style="width:auto;"
                    :disabled="reviewRating < 1"
                    @click="submitMyReview"
                  >
                    {{ myReview ? $t('reviews.update') : $t('reviews.submit') }}
                  </button>
                  <button
                    v-if="myReview"
                    class="btn btn-small btn-danger"
                    style="width:auto;"
                    @click="$emit('requestDeleteReview', myReview)"
                  >
                    {{ $t('common.delete') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BookDetailPage',
  props: {
    selectedBook: { type: Object, required: true },
    currentUser: { type: Object, default: null },

    reviews: { type: Array, default: () => [] },
    reviewsLoading: { type: Boolean, required: true },

    thumb: { type: Function, required: true },
    getBookPrice: { type: Function, required: true },
    isBookBorrowedByMe: { type: Function, required: true },
    isBookUnavailable: { type: Function, required: true },
    isFavorite: { type: Function, required: true },
  },

  data() {
    return {
      reviewRating: 0,
      hoverRating: 0,
      reviewComment: '',
    };
  },

  computed: {
    myReview() {
      if (!this.currentUser || !this.currentUser.id) return null;
      if (!Array.isArray(this.reviews)) return null;
      return this.reviews.find(r => r && String(r.user_id) === String(this.currentUser.id)) || null;
    },

    activeReviewRating() {
      return this.hoverRating > 0 ? this.hoverRating : this.reviewRating;
    },
  },

  watch: {
    myReview: {
      immediate: true,
      handler(v) {
        if (v) {
          this.reviewRating = typeof v.rating === 'number' ? v.rating : Number(v.rating);
          this.reviewComment = v.comment != null ? String(v.comment) : '';
        } else {
          this.reviewRating = 0;
          this.reviewComment = '';
        }

        this.hoverRating = 0;
      },
    },
  },

  methods: {
    submitMyReview() {
      if (!this.selectedBook || !this.selectedBook.id) return;
      this.$emit('submitReview', {
        book_id: this.selectedBook.id,
        rating: this.reviewRating,
        comment: this.reviewComment,
      });
    },
  },
};
</script>
