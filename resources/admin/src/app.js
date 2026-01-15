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

const eventBus = new Vue();
window.EventBus = eventBus;

// Global UI state helpers (loading + toasts) centralizados via EventBus
window.$uiStore = {
  setLoading(key, value) {
    if (!key) return;
    eventBus.$emit('ui:set-loading', { key, value: !!value });
  },
  showToast(type, message) {
    if (!message) return;
    eventBus.$emit('ui:toast', { type, message: String(message) });
  },
};

window.axios = axios;

Vue.config.errorHandler = (err, vm, info) => {
    console.error('Vue Error:', err);
    console.error('Vue Info:', info);

    try {
        const message = err && err.message ? String(err.message) : 'Unexpected error';
        if (window.EventBus && typeof window.EventBus.$emit === 'function') {
            window.EventBus.$emit('global-error', { message, info });
        }
    } catch (e) {
        // se algo falhar no handler global, apenas loga e segue
        console.error('Global error handler failed:', e);
    }
};

Vue.config.warnHandler = (msg, vm, trace) => {
    console.warn('Vue Warning:', msg);
    console.warn('Vue Trace:', trace);
};

new Vue({
  render: (h) => h(App),
}).$mount('#app');
