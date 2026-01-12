<template>
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h2>{{ $t('authors.title') }}</h2>
      <div v-if="currentUser && currentUser.is_admin" style="display:flex; gap:8px; align-items:center;">
        <button
          class="btn btn-small btn-secondary"
          type="button"
          style="width:auto;"
          :disabled="adminAuthorsMode === 'active'"
          @click="$emit('switchAdminAuthorsMode', 'active')"
        >
          {{ $t('common.active') }}
        </button>
        <button
          class="btn btn-small btn-secondary"
          type="button"
          style="width:auto;"
          :disabled="adminAuthorsMode === 'deleted'"
          @click="$emit('switchAdminAuthorsMode', 'deleted')"
        >
          {{ $t('common.deleted') }}
        </button>
        <button
          v-if="adminAuthorsMode !== 'deleted'"
          class="btn btn-small"
          type="button"
          @click="$emit('openCreateAuthor')"
        >
          {{ $t('admin.createAuthor') }}
        </button>
      </div>
    </div>

    <div v-if="!(currentUser && currentUser.is_admin && adminAuthorsMode === 'deleted')" style="margin-bottom: 12px; display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
      <input
        type="text"
        :value="authorsSearchQuery"
        @input="$emit('update:authorsSearchQuery', $event.target.value)"
        :placeholder="$t('authors.search')"
        style="flex:1; min-width: 240px; padding:10px; border:1px solid #dee2e6; border-radius:6px;"
      >
      <select
        :value="authorsSortKey"
        @change="$emit('update:authorsSortKey', $event.target.value)"
        style="padding:10px;border:1px solid #dee2e6;border-radius:6px; min-width:180px;"
      >
        <option value="name">{{ $t('authors.sortNameAsc') }}</option>
        <option value="recent">{{ $t('authors.sortRecent') }}</option>
      </select>
      <select
        :value="authorsPerPage"
        @change="$emit('update:authorsPerPage', Number($event.target.value))"
        style="padding:10px;border:1px solid #dee2e6;border-radius:6px; min-width:160px;"
      >
        <option :value="5">{{ $t('pagination.perPageOption', { n: 5 }) }}</option>
        <option :value="10">{{ $t('pagination.perPageOption', { n: 10 }) }}</option>
        <option :value="20">{{ $t('pagination.perPageOption', { n: 20 }) }}</option>
        <option :value="50">{{ $t('pagination.perPageOption', { n: 50 }) }}</option>
      </select>
    </div>

    <div
      v-if="authorsLoading && !(currentUser && currentUser.is_admin && adminAuthorsMode === 'deleted')"
      class="text-center"
    >
      <LoadingSpinner :text="$t('authors.loading')" />
    </div>

    <div v-if="currentUser && currentUser.is_admin && adminAuthorsMode === 'deleted'">
      <div style="margin-bottom: 12px; display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
        <select
          :value="deletedAuthorsSortKey"
          @change="$emit('update:deletedAuthorsSortKey', $event.target.value)"
          style="padding:10px;border:1px solid #dee2e6;border-radius:6px; min-width:180px;"
        >
          <option value="recent">{{ $t('authors.sortRecent') }}</option>
          <option value="name">{{ $t('authors.sortNameAsc') }}</option>
        </select>
      </div>
      <div v-if="deletedAuthorsLoading" class="text-center">{{ $t('authors.loadingDeleted') }}</div>
      <div v-else>
        <p v-if="!deletedAuthors || deletedAuthors.length === 0" class="text-center" style="color:#666;">
          {{ $t('authors.noDeleted') }}
        </p>
        <div v-else class="book-grid">
          <div
            v-for="author in deletedAuthors"
            :key="author.id"
            class="book-card"
          >
            <img
              :src="thumb(author.photo, 120, 120, 'author')"
              :alt="author.name"
              class="author-photo"
              loading="lazy"
            >
            <div class="book-card-body">
              <h3 class="book-title">{{ author.name }}</h3>
              <p class="book-author">{{ $t('authors.booksCountText', { n: author.books && author.books.length ? author.books.length : 0 }) }}</p>
              <div style="margin-top:10px; display:flex; gap:8px;">
                <button class="btn btn-small" style="width:auto;" @click.stop="$emit('restoreDeletedAuthor', author.id)">
                  {{ $t('common.restore') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="!(currentUser && currentUser.is_admin && adminAuthorsMode === 'deleted')">
      <div class="book-grid">
        <div
          v-for="author in paginatedAuthors"
          :key="author.id"
          class="book-card"
          style="cursor: pointer;"
          @click="$emit('select-author', author)"
        >
          <img
            :src="thumb(author.photo, 120, 120, 'author')"
            :alt="author.name"
            class="author-photo"
            loading="lazy"
          >
          <div class="book-card-body">
            <h3 class="book-title">{{ author.name }}</h3>
            <p class="book-author">{{ $t('authors.booksCountText', { n: author.books && author.books.length ? author.books.length : 0 }) }}</p>
            <div v-if="currentUser && currentUser.is_admin" style="margin-top:10px; display:flex; gap:8px;">
              <button class="btn btn-small" @click.stop="$emit('openEditAuthor', author)">{{ $t('common.edit') }}</button>
              <button class="btn btn-small btn-danger" @click.stop="$emit('askDeleteAuthor', author.id)">{{ $t('common.delete') }}</button>
            </div>
          </div>
        </div>
      </div>

      <div class="pagination" v-if="totalAuthorsPages > 1">
        <button
          class="page-link"
          @click="$emit('changeAuthorsPage', authorsPage - 1)"
          :disabled="authorsPage === 1"
        >
          {{ $t('pagination.previous') }}
        </button>

        <button class="page-link" :class="{ active: authorsPage === 1 }" @click="$emit('changeAuthorsPage', 1)">
          1
        </button>

        <span v-if="authorsPage > 3" class="page-link" style="cursor: default; background: transparent; border-color: transparent;">
          ...
        </span>

        <button
          v-if="authorsPage - 1 > 1"
          class="page-link"
          @click="$emit('changeAuthorsPage', authorsPage - 1)"
        >
          {{ authorsPage - 1 }}
        </button>

        <button
          v-if="authorsPage !== 1 && authorsPage !== totalAuthorsPages"
          class="page-link active"
          @click="$emit('changeAuthorsPage', authorsPage)"
        >
          {{ authorsPage }}
        </button>

        <button
          v-if="authorsPage + 1 < totalAuthorsPages"
          class="page-link"
          @click="$emit('changeAuthorsPage', authorsPage + 1)"
        >
          {{ authorsPage + 1 }}
        </button>

        <span v-if="authorsPage < totalAuthorsPages - 2" class="page-link" style="cursor: default; background: transparent; border-color: transparent;">
          ...
        </span>

        <button
          v-if="totalAuthorsPages > 1"
          class="page-link"
          :class="{ active: authorsPage === totalAuthorsPages }"
          @click="$emit('changeAuthorsPage', totalAuthorsPages)"
        >
          {{ totalAuthorsPages }}
        </button>

        <button
          class="page-link"
          @click="$emit('changeAuthorsPage', authorsPage + 1)"
          :disabled="authorsPage === totalAuthorsPages"
        >
          {{ $t('pagination.next') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AuthorsPage',
  props: {
    currentUser: { type: Object, default: null },
    authorsLoading: { type: Boolean, required: true },

    adminAuthorsMode: { type: String, default: 'active' },
    deletedAuthors: { type: Array, default: () => [] },
    deletedAuthorsLoading: { type: Boolean, default: false },
    deletedAuthorsSortKey: { type: String, required: true },

    authorsSearchQuery: { type: String, required: true },
    authorsSortKey: { type: String, required: true },
    authorsPerPage: { type: Number, required: true },

    paginatedAuthors: { type: Array, required: true },
    totalAuthorsPages: { type: Number, required: true },
    authorsPage: { type: Number, required: true },

    thumb: { type: Function, required: true },
  },
};
</script>
