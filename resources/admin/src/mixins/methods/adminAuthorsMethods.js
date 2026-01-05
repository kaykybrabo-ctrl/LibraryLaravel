export default {
  methods: {
    openCreateAuthorModal() {
      this.newAuthor = {
        name: '',
        bio: '',
        photo: '',
      };
      this.showCreateAuthorModal = true;
    },

    closeCreateAuthorModal() {
      this.showCreateAuthorModal = false;
      this.newAuthor = {
        name: '',
        bio: '',
        photo: '',
      };
    },

    async createAuthor() {
      try {
        if (!this.newAuthor.name) return;
        await this.graphql(
          'mutation CreateAuthor($name: String!, $bio: String, $photo: String) { createAuthor(name: $name, bio: $bio, photo: $photo) { id } }',
          this.newAuthor
        );
        await this.loadAuthors();
        this.closeCreateAuthorModal();
      } catch (e) {

      }
    },

    openEditAuthor(author) {
      this.editAuthor = {
        id: author.id,
        name: author.name,
        bio: author.bio || '',
        photo: author.photo || '',
      };
      this.showEditAuthorModal = true;
    },

    closeEditAuthorModal() {
      this.showEditAuthorModal = false;
      this.editAuthor = null;
    },

    async saveEditAuthor() {
      try {
        if (!this.editAuthor || !this.editAuthor.name) return;
        await this.graphql(
          'mutation UpdateAuthor($id: ID!, $name: String!, $bio: String, $photo: String) { updateAuthor(id: $id, name: $name, bio: $bio, photo: $photo) { id } }',
          this.editAuthor
        );
        await this.loadAuthors();
        await this.loadBooks();
        this.closeEditAuthorModal();
      } catch (e) {

      }
    },

    async deleteAuthor(authorId) {
      try {
        await this.graphql(
          'mutation DeleteAuthor($id: ID!) { deleteAuthor(id: $id) }',
          { id: authorId }
        );
        await this.loadAuthors();
        await this.loadBooks();
      } catch (e) {

      }
    },
  },
};
