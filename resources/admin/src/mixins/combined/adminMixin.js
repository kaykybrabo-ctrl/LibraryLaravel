import adminBooksMethods from '../methods/adminBooksMethods.js';
import adminAuthorsMethods from '../methods/adminAuthorsMethods.js';
import adminUsersMethods from '../methods/adminUsersMethods.js';
import profileMethods from '../methods/profileMethods.js';

export default {
  methods: {
    ...adminBooksMethods.methods,
    ...adminAuthorsMethods.methods,
    ...adminUsersMethods.methods,
    ...profileMethods.methods,
  },
};
