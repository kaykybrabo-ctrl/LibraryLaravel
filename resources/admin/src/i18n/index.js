import ptBR from './pt-BR.js';
import enUS from './en-US.js';

const translations = {
  'pt-BR': ptBR,
  'en-US': enUS,
};

const getBrowserLanguage = () => {
  if (typeof window !== 'undefined') {
    const lang = navigator.language || navigator.userLanguage;
    return lang.startsWith('pt') ? 'pt-BR' : 'en-US';
  }
  return 'pt-BR'; 
};

let currentLanguage = getBrowserLanguage();

export const t = (key, params = {}) => {
  const keys = key.split('.');
  let value = translations[currentLanguage];
  
  for (const k of keys) {
    value = value?.[k];
  }
  
  if (!value) return key;
  
  Object.keys(params).forEach(param => {
    value = value.replace(`{${param}}`, params[param]);
  });
  
  return value;
};

export const getCurrentLanguage = () => currentLanguage;

export const setLanguage = (lang) => {
  if (translations[lang]) {
    currentLanguage = lang;
    if (typeof window !== 'undefined') {
      localStorage.setItem('language', lang);
      window.location.reload();
    }
  }
};

export default {
  t,
  getCurrentLanguage,
  setLanguage
};
