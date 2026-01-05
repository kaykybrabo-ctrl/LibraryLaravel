export default {
  methods: {
    authHeaders() {
      return this.authToken
        ? { Authorization: `Bearer ${this.authToken}` }
        : {};
    },

    async graphql(query, variables = {}) {
      try {
        const response = await fetch('/graphql', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            ...this.authHeaders(),
          },
          body: JSON.stringify({ query, variables }),
        });
        const json = await response.json();
        if (json.errors) {
          const err = json.errors[0];
          throw new Error(err.message || 'GraphQL error');
        }
        return json.data;
      } catch (e) {
        if (!(e && e.message && String(e.message).toLowerCase().includes('unauthenticated'))) {
          console.error('GraphQL request failed:', e);
        }
        throw e;
      }
    },
  },
};
