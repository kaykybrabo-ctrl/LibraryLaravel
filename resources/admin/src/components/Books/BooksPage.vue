<template>
  <div class="container">
    <div
      style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;"
    >
      <h2>{{ $t('navigation.books') }}</h2>
      <div v-if="currentUser && currentUser.is_admin" style="display:flex; gap:8px; align-items:center;">
        <button
          class="btn btn-small btn-secondary"
          type="button"
          style="width:auto;"
          :disabled="adminBooksMode === 'active'"
          @click="$emit('switchAdminBooksMode', 'active')"
        >
          {{ $t('common.active') }}
        </button>
        <button
          class="btn btn-small btn-secondary"
          type="button"
          style="width:auto;"
          :disabled="adminBooksMode === 'deleted'"
          @click="$emit('switchAdminBooksMode', 'deleted')"
        >
          {{ $t('common.deleted') }}
        </button>
        <button
          v-if="adminBooksMode !== 'deleted'"
          class="btn btn-small"
          type="button"
          @click="$emit('openCreateBookModal')"
        >
          {{ $t('admin.createBook') }}
        </button>
      </div>
    </div>

    <div
      v-if="!(currentUser && currentUser.is_admin && adminBooksMode === 'deleted')"
      style="display:grid; grid-template-columns: 1fr 220px 200px 160px; gap:12px; margin-bottom: 12px;"
    >
      <input
        type="text"
        :value="searchQuery"
        @input="$emit('update:searchQuery', $event.target.value)"
        :placeholder="$t('books.search')"
        style="padding:10px;border:1px solid #dee2e6;border-radius:6px;"
      >
      <select
        :value="authorFilterId"
        @change="$emit('update:authorFilterId', $event.target.value)"
        style="padding:10px;border:1px solid #dee2e6;border-radius:6px;"
      >
        <option value="">{{ $t('books.allAuthors') }}</option>
        <option v-for="a in authors" :key="a.id" :value="a.id">{{ a.name }}</option>
      </select>
      <select
        :value="sortKey"
        @change="$emit('update:sortKey', $event.target.value)"
        style="padding:10px;border:1px solid #dee2e6;border-radius:6px;"
      >
        <option value="title">{{ $t('books.sortTitle') }} (A-Z)</option>
        <option value="recent">{{ $t('books.sortRecent') }}</option>
      </select>
      <select
        :value="booksPerPage"
        @change="$emit('update:booksPerPage', Number($event.target.value))"
        style="padding:10px;border:1px solid #dee2e6;border-radius:6px;"
      >
        <option :value="5">{{ $t('pagination.perPageOption', { n: 5 }) }}</option>
        <option :value="10">{{ $t('pagination.perPageOption', { n: 10 }) }}</option>
        <option :value="20">{{ $t('pagination.perPageOption', { n: 20 }) }}</option>
        <option :value="50">{{ $t('pagination.perPageOption', { n: 50 }) }}</option>
      </select>
    </div>

    <div v-if="currentUser && currentUser.is_admin && adminBooksMode === 'deleted'">
      <div style="margin-bottom: 12px; display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
        <select
          :value="deletedBooksSortKey"
          @change="$emit('update:deletedBooksSortKey', $event.target.value)"
          style="padding:10px;border:1px solid #dee2e6;border-radius:6px; min-width:180px;"
        >
          <option value="recent">{{ $t('books.sortRecent') }}</option>
          <option value="title">{{ $t('books.sortTitle') }} (A-Z)</option>
        </select>
      </div>
      <div v-if="deletedBooksLoading">
        <p class="text-center" style="color:#666;">{{ $t('books.loadingDeleted') }}</p>
      </div>
      <div v-else>
        <p v-if="!deletedBooks || deletedBooks.length === 0" class="text-center" style="color:#666;">
          {{ $t('books.noDeleted') }}
        </p>

        <div v-else class="book-grid">
          <div
            v-for="book in deletedBooks"
            :key="book.id"
            class="book-card"
          >
            <img
              :src="thumb(book.photo, 600, 360, 'book')"
              :alt="book.title"
              @error="$event.target.src = thumb('', 600, 360, 'book')"
              loading="lazy"
            >
            <div class="book-card-body">
              <h3 class="book-title">{{ book.title }}</h3>
              <p class="book-author">
                <span v-if="book.author">{{ book.author.name }}</span>
                <span v-else>{{ $t('books.unknownAuthor') }}</span>
              </p>
              <p class="book-desc">{{ book.description || $t('books.noDescription') }}</p>
              <div style="margin-top:10px; display:flex; gap:8px;">
                <button class="btn btn-small" style="width:auto;" @click.stop="$emit('restoreDeletedBook', book.id)">
                  {{ $t('common.restore') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="loading">
      <p class="text-center" style="color:#666;">{{ $t('books.loading') }}</p>
    </div>

    <div v-else-if="!(currentUser && currentUser.is_admin && adminBooksMode === 'deleted')">
      <p
        v-if="paginatedBooks.length === 0"
        class="text-center"
        style="color:#666;"
      >
        {{ $t('books.noBooksFound') }}
      </p>

      <div class="book-grid">
        <div
          v-for="book in paginatedBooks"
          :key="book.id"
          class="book-card"
          :class="{ borrowed: isBookUnavailable(book.id) }"
          @click="$emit('viewBook', book)"
        >
          <div v-if="isBookUnavailable(book.id)" class="banner-rented">{{ $t('books.bannerRented') }}</div>

          <button
            v-if="currentUser && !currentUser.is_admin"
            class="fav-chip"
            :class="{ active: isFavorite(book.id) }"
            @click.stop="$emit('toggleFavorite', book)"
            :aria-label="isFavorite(book.id) ? $t('books.removeFromFavorites') : $t('books.addToFavorites')"
            :title="isFavorite(book.id) ? $t('books.removeFromFavorites') : $t('books.addToFavorites')"
          >
            ❤
          </button>

          <img
            :src="thumb(book.photo, 600, 360, 'book')"
            :alt="book.title"
            @error="$event.target.src = thumb('', 600, 360, 'book')"
            loading="lazy"
          >

          <div class="book-card-body">
            <h3 class="book-title">{{ book.title }}</h3>
            <p class="book-author">
              <span
                v-if="book.author"
                @click.stop="$emit('select-author', book.author)"
                style="cursor:pointer;color:#162c74;text-decoration:underline;"
              >
                {{ book.author.name }}
              </span>
              <span v-else>{{ $t('books.unknownAuthor') }}</span>
            </p>
            <p class="book-desc">{{ book.description || $t('books.noDescription') }}</p>
            <p style="margin-top:6px; font-weight:600; color:#162c74;">
              {{ $formatCurrency(getBookPrice(book)) }}
            </p>

            <div
              v-if="currentUser && !currentUser.is_admin"
              style="margin-top:10px; display:flex; flex-direction:column; gap:8px;"
            >
              <button
                class="btn btn-small"
                :disabled="isBookUnavailable(book.id) && !isBookBorrowedByMe(book.id)"
                @click.stop="$emit('rentOrReturn', book)"
              >
                {{ isBookBorrowedByMe(book.id) ? $t('books.return') : (isBookUnavailable(book.id) ? $t('books.unavailable') : $t('books.rent')) }}
              </button>
              <small v-if="isBookBorrowedByMe(book.id)" style="font-size:0.8rem; color:#16a34a;">{{ $t('books.borrowedByMe') }}</small>
              <small v-else-if="isBookUnavailable(book.id)" style="font-size:0.8rem; color:#b91c1c;">{{ $t('books.borrowedByOther') }}</small>
              <button class="btn btn-small btn-secondary" @click.stop="$emit('addToCart', book)">
                {{ $t('books.addToCart') }}
              </button>
            </div>

            <div v-else style="margin-top: 10px;">
              <button v-if="!currentUser" class="btn btn-small" @click.stop="$emit('goLogin')">{{ $t('books.loginToRentOrBuy') }}</button>
              <div v-else-if="currentUser && currentUser.is_admin" style="padding:8px 0; color:#555; font-size:0.9rem;">{{ $t('books.adminNoRentFavorite') }}</div>
            </div>

            <div v-if="currentUser && currentUser.is_admin" style="margin-top:10px; display:flex; gap:8px;">
              <button class="btn btn-small" @click.stop="$emit('openEditBook', book)">{{ $t('common.edit') }}</button>
              <button class="btn btn-small btn-danger" @click.stop="$emit('askDeleteBook', book.id)">{{ $t('common.delete') }}</button>
            </div>

            <div v-if="currentUser && !currentUser.is_admin && isFavorite(book.id)" class="fav-chip-bottom">{{ $t('books.favorite') }}</div>
          </div>
        </div>
      </div>

      <div class="pagination" v-if="totalPages > 1">
        <button
          class="page-link"
          @click="$emit('changePage', booksPage - 1)"
          :disabled="booksPage === 1"
        >
          {{ $t('pagination.previous') }}
        </button>

        <button class="page-link" :class="{ active: booksPage === 1 }" @click="$emit('changePage', 1)">
          1
        </button>

        <span v-if="booksPage > 3" class="page-link" style="cursor: default; background: transparent; border-color: transparent;">
          ...
        </span>

        <button
          v-if="booksPage - 1 > 1"
          class="page-link"
          @click="$emit('changePage', booksPage - 1)"
        >
          {{ booksPage - 1 }}
        </button>

        <button
          v-if="booksPage !== 1 && booksPage !== totalPages"
          class="page-link active"
          @click="$emit('changePage', booksPage)"
        >
          {{ booksPage }}
        </button>

        <button
          v-if="booksPage + 1 < totalPages"
          class="page-link"
          @click="$emit('changePage', booksPage + 1)"
        >
          {{ booksPage + 1 }}
        </button>

        <span v-if="booksPage < totalPages - 2" class="page-link" style="cursor: default; background: transparent; border-color: transparent;">
          ...
        </span>

        <button
          v-if="totalPages > 1"
          class="page-link"
          :class="{ active: booksPage === totalPages }"
          @click="$emit('changePage', totalPages)"
        >
          {{ totalPages }}
        </button>
        <button
          class="page-link"
          @click="$emit('changePage', booksPage + 1)"
          :disabled="booksPage === totalPages"
        >
          {{ $t('pagination.next') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BooksPage',
  props: {
    currentUser: { type: Object, default: null },
    authors: { type: Array, required: true },
    loading: { type: Boolean, required: true },

    adminBooksMode: { type: String, default: 'active' },
    deletedBooks: { type: Array, default: () => [] },
    deletedBooksLoading: { type: Boolean, default: false },
    deletedBooksSortKey: { type: String, required: true },

    searchQuery: { type: String, required: true },
    authorFilterId: { type: String, required: true },
    sortKey: { type: String, required: true },
    booksPerPage: { type: Number, required: true },

    paginatedBooks: { type: Array, required: true },
    totalPages: { type: Number, required: true },
    booksPage: { type: Number, required: true },

    thumb: { type: Function, required: true },
    getBookPrice: { type: Function, required: true },
    isFavorite: { type: Function, required: true },
    isBookUnavailable: { type: Function, required: true },
    isBookBorrowedByMe: { type: Function, required: true },
  },
};
</script>
