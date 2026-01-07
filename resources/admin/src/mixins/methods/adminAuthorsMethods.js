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
          'mutation CreateAuthor($input: CreateAuthorInput!) { createAuthor(input: $input) { id } }',
          { input: this.newAuthor }
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
          'mutation UpdateAuthor($id: ID!, $input: UpdateAuthorInput!) { updateAuthor(id: $id, input: $input) { id } }',
          {
            id: this.editAuthor.id,
            input: {
              name: this.editAuthor.name,
              bio: this.editAuthor.bio,
              photo: this.editAuthor.photo,
            },
          }
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
          'mutation DeleteAuthor($id: ID!) { deleteAuthor(id: $id) { message } }',
          { id: authorId }
        );
        await this.loadAuthors();
        await this.loadBooks();
      } catch (e) {

      }
    },
  },
};
