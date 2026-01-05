export default {
  methods: {
    async saveProfile() {
      try {
        if (!this.currentUser) return;
        const payload = {
          name: this.profileFormName,
          photo: this.profileFormPhoto || (this.currentUser && this.currentUser.photo) || '',
        };
        const data = await this.graphql(
          'mutation UpdateProfile($input: UpdateProfileInput!) { updateProfile(input: $input) { id name email is_admin photo } }',
          { input: payload },
        );
        const updated = data && data.updateProfile ? data.updateProfile : this.currentUser;
        this.currentUser = updated;
        if (typeof window !== 'undefined') {
          localStorage.setItem('currentUser', JSON.stringify(updated));
        }
        this.editingProfile = false;
      } catch (e) {

      }
    },
  },
};
