export default {
  computed: {
    minStartDate() {
      const today = new Date();
      return today.toISOString().split('T')[0];
    },

    minEndDate() {
      const today = new Date();
      today.setDate(today.getDate() + 1);
      return today.toISOString().split('T')[0];
    },

    maxDate2030() {
      return '2030-12-31';
    },
  },
};
