/**
 * i18n.js
 *
 * This will setup the i18n language files and locale data for your app.
 *
 *   IMPORTANT: This file is used by the internal build
 *   script `extract-intl`, and must use CommonJS module syntax
 *   You CANNOT use import/export in this file.
 */
const addLocaleData = require("react-intl").addLocaleData; //eslint-disable-line
const enLocaleData = require("react-intl/locale-data/en");
const viLocaleData = require("react-intl/locale-data/vi");

//! Comment this 2 line to extract translation
const enTranslationMessages = require("json-loader!./translations/en.json");
const viTranslationMessages = require("json-loader!./translations/vi.json");

// //! Uncomment this 2 line to extract translation
// const enTranslationMessages = require("./translations/en.json");
// const viTranslationMessages = require("./translations/vi.json");

addLocaleData(enLocaleData);
addLocaleData(viLocaleData);

const DEFAULT_LOCALE = "vi";

// prettier-ignore
const appLocales = [
  "vi",
  "en",
];

const formatTranslationMessages = (locale, messages) => {
  const defaultFormattedMessages =
    locale !== DEFAULT_LOCALE
      ? formatTranslationMessages(DEFAULT_LOCALE, enTranslationMessages)
      : {};
  const flattenFormattedMessages = (formattedMessages, key) => {
    const formattedMessage =
      !messages[key] && locale !== DEFAULT_LOCALE
        ? defaultFormattedMessages[key]
        : messages[key];
    return Object.assign(formattedMessages, { [key]: formattedMessage });
  };
  return Object.keys(messages).reduce(flattenFormattedMessages, {});
};

const translationMessages = {
  vi: formatTranslationMessages("vi", viTranslationMessages),
  en: formatTranslationMessages("en", enTranslationMessages),
};

exports.appLocales = appLocales;
exports.formatTranslationMessages = formatTranslationMessages;
exports.translationMessages = translationMessages;
exports.DEFAULT_LOCALE = DEFAULT_LOCALE;
