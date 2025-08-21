import React, { createContext, useContext, useState } from 'react';
import { Platform } from 'react-native';

const SettingsContext = createContext();

const translations = {
  en: require('../lang/en.json'),
  es: require('../lang/es.json'),
};

export function SettingsProvider({ children }) {
  const deviceLocale = Platform.OS === 'web'
    ? navigator.language
    : Intl.DateTimeFormat().resolvedOptions().locale;
  const initialLang = deviceLocale && deviceLocale.startsWith('es') ? 'es' : 'en';
  const [language, setLanguage] = useState(initialLang);
  const [currency, setCurrency] = useState(initialLang === 'es' ? 'ARS' : 'USD');

  const t = (key) => translations[language][key] || key;

  return (
    <SettingsContext.Provider value={{ language, setLanguage, currency, setCurrency, t }}>
      {children}
    </SettingsContext.Provider>
  );
}

export function useSettings() {
  return useContext(SettingsContext);
}

