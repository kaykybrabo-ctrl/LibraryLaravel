export default {
  methods: {
    toggleDropdown() {
      this.showDropdown = !this.showDropdown;
    },

    closeDropdown() {
      this.showDropdown = false;
    },

    askDelete(type, message, callback) {
      this.confirmTitle = type === 'book' ? 'Excluir Livro' : type === 'author' ? 'Excluir Autor' : 'Confirmar Ação';
      this.confirmMessage = message || 'Esta ação não pode ser desfeita.';
      this.confirmConfirmLabel = 'Confirmar';
      this.confirmCancelLabel = 'Cancelar';
      this.confirmIsDanger = true;
      this.confirmCallback = typeof callback === 'function' ? callback : null;
      this.showConfirmModal = true;
    },

    closeConfirmModal() {
      this.showConfirmModal = false;
      this.confirmTitle = '';
      this.confirmMessage = '';
      this.confirmConfirmLabel = '';
      this.confirmCancelLabel = '';
      this.confirmIsDanger = false;
      this.confirmCallback = null;
    },

    confirmModalProceed() {
      if (this.confirmCallback) {
        this.confirmCallback();
      }
      this.closeConfirmModal();
    },

    openUpload(target) {
      this.uploadTarget = target;
      this.showUploadModal = true;
    },

    closeUploadModal() {
      this.showUploadModal = false;
      this.uploadTarget = null;
    },

    handleUploadSuccess(publicId) {
      if (this.uploadTarget === 'profile') {
        this.profileFormPhoto = publicId;
      } else if (this.uploadTarget === 'book') {
        this.newBook.photo = publicId;
      } else if (this.uploadTarget === 'book_edit' && this.editBook) {
        this.editBook.photo = publicId;
      } else if (this.uploadTarget === 'author') {
        this.newAuthor.photo = publicId;
      } else if (this.uploadTarget === 'author_edit' && this.editAuthor) {
        this.editAuthor.photo = publicId;
      }
    },

    async saveProfile() {
      if (!this.currentUser) return;
      try {
        await this.graphql(
          'mutation UpdateProfile($name: String!, $photo: String) { updateProfile(name: $name, photo: $photo) { id name email photo } }',
          { name: this.profileFormName, photo: this.profileFormPhoto }
        );
        this.currentUser.name = this.profileFormName;
        this.currentUser.photo = this.profileFormPhoto;
        if (typeof window !== 'undefined') {
          localStorage.setItem('currentUser', JSON.stringify(this.currentUser));
        }
        this.editingProfile = false;
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

    goToLanding() {
      if (typeof window !== 'undefined') {
        window.location.href = '/';
      }
    },
  },
};
