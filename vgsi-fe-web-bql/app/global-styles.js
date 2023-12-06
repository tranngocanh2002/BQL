import { createGlobalStyle } from 'styled-components';

const GlobalStyle = createGlobalStyle`
  html,
  body {
    height: 100%;
    width: 100%;
    font-family: "Segoe UI", "SegoeuiPc", "San Francisco", "Helvetica Neue", "Helvetica", "Lucida Grande", "Roboto", "Ubuntu", "Tahoma", Microsoft Sans Serif, Tahoma, Arial, sans-serif;
  }


  #app {
    background-color: #EFF1F4;
    min-height: 100%;
    min-width: 100%;
  }

  p,
  label {
    line-height: 1.5em;
  }

`;

export default GlobalStyle;