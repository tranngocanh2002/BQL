module.exports = {
  parser: "babel-eslint",
  env: {
    browser: true,
    node: true,
  },
  extends: ["eslint:recommended", "plugin:react/recommended"],
  parserOptions: {
    ecmaFeatures: {
      jsx: true,
      legacyDecorators: true,
    },
    ecmaVersion: 2018,
    sourceType: "module",
  },
  plugins: ["react"],
  rules: {
    quotes: ["error", "double"],
    semi: ["error", "always"],
    "no-unused-vars": "warn",
    "react/no-deprecated": "warn",
    "no-console": "warn",
    "react/prop-types": "off",
    "react/no-deprecated": "off",
  },
};
