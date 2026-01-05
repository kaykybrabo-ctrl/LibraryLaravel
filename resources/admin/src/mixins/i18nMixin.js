import { t, getCurrentLanguage } from '../i18n/index.js';

export default {
  data() {
    return {
      currentLang: getCurrentLanguage(),
    };
  },
  
  methods: {
    $t(key, params = {}) {
      return t(key, params);
    },
    
    $formatMessage(message, params = {}) {
      return Object.keys(params).reduce((str, param) => {
        return str.replace(new RegExp(`{${param}}`, 'g'), params[param]);
      }, message);
    },
    
    getCurrentLanguage() {
      return this.currentLang;
    },
    
    isPortuguese() {
      return this.currentLang === 'pt-BR';
    },
    
    isEnglish() {
      return this.currentLang === 'en-US';
    },
    
    $formatCurrency(value) {
      if (this.isPortuguese()) {
        return new Intl.NumberFormat('pt-BR', {
          style: 'currency',
          currency: 'BRL',
        }).format(value);
      } else {
        return new Intl.NumberFormat('en-US', {
          style: 'currency',
          currency: 'USD',
        }).format(value);
      }
    },
    
    $formatDate(date) {
      const options = { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit' 
      };
      
      if (this.isPortuguese()) {
        return new Intl.DateTimeFormat('pt-BR', options).format(new Date(date));
      } else {
        return new Intl.DateTimeFormat('en-US', options).format(new Date(date));
      }
    },
    
    $formatDateTime(date) {
      const options = { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      };
      
      if (this.isPortuguese()) {
        return new Intl.DateTimeFormat('pt-BR', options).format(new Date(date));
      } else {
        return new Intl.DateTimeFormat('en-US', options).format(new Date(date));
      }
    },
  },
};
