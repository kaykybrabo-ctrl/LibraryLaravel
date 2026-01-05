import authData from '../data/authData.js';
import booksData from '../data/booksData.js';
import modalsData from '../data/modalsData.js';
import adminData from '../data/adminData.js';

export default {
  data() {
    return {
      ...authData.data(),
      ...booksData.data(),
      ...modalsData.data(),
      ...adminData.data(),
    };
  },
};
