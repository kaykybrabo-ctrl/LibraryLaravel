import booksComputed from '../computed/booksComputed.js';
import cartComputed from '../computed/cartComputed.js';
import loansComputed from '../computed/loansComputed.js';
import usersComputed from '../computed/usersComputed.js';
import datesComputed from '../computed/datesComputed.js';

export default {
  computed: {
    ...booksComputed.computed,
    ...cartComputed.computed,
    ...loansComputed.computed,
    ...usersComputed.computed,
    ...datesComputed.computed,
  },
};
