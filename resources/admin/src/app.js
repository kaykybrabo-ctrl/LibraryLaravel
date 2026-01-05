import Vue from 'vue';
import axios from 'axios';

import App from './components/App.vue';
import Modal from './components/common/Modal.vue';
import LoadingSpinner from './components/common/LoadingSpinner.vue';
import ConfirmModal from './components/Modals/ConfirmModal.vue';

import i18nMixin from './mixins/i18nMixin.js';

window.Vue = Vue;

Vue.mixin(i18nMixin);

Vue.component('Modal', Modal);
Vue.component('LoadingSpinner', LoadingSpinner);
Vue.component('ConfirmModal', ConfirmModal);

window.axios = axios;

Vue.config.errorHandler = (err, vm, info) => {
    console.error('Vue Error:', err);
    console.error('Vue Info:', info);
};

Vue.config.warnHandler = (msg, vm, trace) => {
    console.warn('Vue Warning:', msg);
    console.warn('Vue Trace:', trace);
};

new Vue({
  render: (h) => h(App),
}).$mount('#app');
