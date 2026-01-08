import utilsMixin from './utilsMixin';
import dataMixin from './combined/dataMixin';
import computedMixin from './combined/computedMixin';
import authMixin from './authMixin';
import loadersMixin from './loadersMixin';
import booksMixin from './combined/booksMixin';
import adminMixin from './adminMixin';
import uiMixin from './uiMixin';
import lifecycleMixin from './lifecycleMixin';

export default {
  mixins: [
    utilsMixin,
    dataMixin,
    computedMixin,
    authMixin,
    loadersMixin,
    booksMixin,
    adminMixin,
    uiMixin,
    lifecycleMixin,
  ],
};
