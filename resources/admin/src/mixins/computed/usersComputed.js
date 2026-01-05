export default {
  computed: {
    totalUsersPages() {
      return Math.ceil(this.totalUsers / this.usersPerPage);
    },

    paginatedUsers() {
      const start = (this.usersPage - 1) * this.usersPerPage;
      return this.users.slice(start, start + this.usersPerPage);
    },

    formatOrderStatus() {
      return (status) => {
        const map = {
          pending: 'Pendente',
          paid: 'Pago',
          canceled: 'Cancelado',
        };
        return map[status] || status;
      };
    },

    formatDate() {
      return (dateStr) => {
        if (!dateStr) return '';
        try {
          const d = new Date(dateStr);
          if (isNaN(d.getTime())) return '';
          return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        } catch (e) {
          return '';
        }
      };
    },
  },
};
