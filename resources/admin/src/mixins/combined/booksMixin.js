import booksMethods from '../methods/booksMethods.js';
import favoritesMethods from '../methods/favoritesMethods.js';
import cartMethods from '../methods/cartMethods.js';
import rentalMethods from '../methods/rentalMethods.js';
import reviewsMethods from '../methods/reviewsMethods.js';

export default {
  methods: {
    ...booksMethods.methods,
    ...favoritesMethods.methods,
    ...cartMethods.methods,
    ...rentalMethods.methods,
    ...reviewsMethods.methods,
  },
};
