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

        let json = null;
        try {
          json = await response.json();
        } catch (err) {
          const e = new Error('Invalid JSON response');
          e.status = response.status;
          e.originalError = err;
          throw e;
        }

        if (json && Array.isArray(json.errors) && json.errors.length) {
          const err = json.errors[0] || {};
          const e = new Error(err.message || 'GraphQL error');
          e.graphQLErrors = json.errors;
          e.extensions = err.extensions || null;
          e.code = err.extensions && err.extensions.code ? err.extensions.code : undefined;
          e.meta = err.extensions && err.extensions.meta ? err.extensions.meta : undefined;
          e.validationErrors =
            err.extensions && err.extensions.meta && err.extensions.meta.errors
              ? err.extensions.meta.errors
              : undefined;
          throw e;
        }

        if (!response.ok) {
          const e = new Error('Request failed');
          e.status = response.status;
          e.body = json;
          throw e;
        }

        return json ? json.data : null;
      } catch (e) {
        if (!(e && e.message && String(e.message).toLowerCase().includes('unauthenticated'))) {
          console.error('GraphQL request failed:', e);
        }
        throw e;
      }
    },
  },
};
