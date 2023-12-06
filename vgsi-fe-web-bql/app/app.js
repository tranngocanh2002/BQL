/**
 * app.js
 *
 * This is the entry file for the application, only setup and boilerplate
 * code.
 */

// Needed for redux-saga es6 generator support
import "@babel/polyfill";

// Import all the third party stuff
import { ConnectedRouter } from "connected-react-router/immutable";
import React from "react";
import ReactDOM from "react-dom";
import { Provider } from "react-redux";
import "sanitize.css/sanitize.css";
import history from "utils/history";
import "sanitize.css/sanitize.css";

// Import Sentry
import * as Sentry from "@sentry/browser";

// Import SCSS
// import "antd/dist/antd.css";
import "ant-design-pro/dist/ant-design-pro.css";
import "themes/result.css";

// Import root app
import App from "containers/App";

// Import Language Provider
import LanguageProvider from "containers/LanguageProvider";

// Load the favicon and the .htaccess file
/* eslint-disable import/no-unresolved, import/extensions */
import "!file-loader?name=[name].[ext]!./OneSignalSDKUpdaterWorker.js";
import "!file-loader?name=[name].[ext]!./OneSignalSDKWorker.js";
import "!file-loader?name=[name].[ext]!./images/icon-514.jpg";
import "!file-loader?name=[name].[ext]!./images/logo_waterpoint@1x.png";
import "!file-loader?name=[name].[ext]!./manifest.json";
import "file-loader?name=.htaccess!./.htaccess";
import $ from "jquery";
/* eslint-enable import/no-unresolved, import/extensions */

import configureStore from "./configureStore";

// Import i18n messages
import { translationMessages } from "./i18n";
import { saveTokenNoti } from "./redux/actions/config";

// Create redux store with history
const initialState = {};
export const store = configureStore(initialState, history);
const MOUNT_NODE = document.getElementById("app");

document.addEventListener("mouseup", function () {
  let focus = $(":focus");
  if (!!focus && !!focus[0] && focus[0].localName == "button") {
    focus.blur();
  }
});

const render = (messages) => {
  ReactDOM.render(
    <Provider store={store}>
      <LanguageProvider messages={messages}>
        <ConnectedRouter history={history}>
          <App />
        </ConnectedRouter>
      </LanguageProvider>
    </Provider>,
    MOUNT_NODE
  );
};

if (module.hot) {
  // Hot reloadable React components and translation json files
  // modules.hot.accept does not accept dynamic dependencies,
  // have to be constants at compile-time
  module.hot.accept(["./i18n", "containers/App"], () => {
    ReactDOM.unmountComponentAtNode(MOUNT_NODE);
    render(translationMessages);
  });
}

// Chunked polyfill for browsers without Intl support
if (!window.Intl) {
  new Promise((resolve) => {
    resolve(import("intl"));
  })
    .then(() => Promise.all([import("intl/locale-data/jsonp/en.js")]))
    .then(() => render(translationMessages))
    .catch((err) => {
      throw err;
    });
} else {
  render(translationMessages);
}

// setInterval(() => {

//   store.dispatch(addNotification({
//     title: `data.heading`,
//     content: `data.content data.content data.content data.content data.content data.content data.content`,
//     date: moment()
//   }))
// }, 5000);
window.installOneSignal = (appId) => {
  console.log("installOneSignal");
  var OneSignal = window.OneSignal || [];
  OneSignal.push(() => {
    OneSignal.init({
      appId: appId,
      autoResubscribe: true,
      allowLocalhostAsSecureOrigin: true,
      persistNotification: true,
      notifyButton: {
        enable: false,
      },
    });
    OneSignal.showNativePrompt();
    OneSignal.getUserId().then((userId) => {
      console.log("getUserId 111", userId);
      if (userId) {
        OneSignal.setSubscription(true);
        store.dispatch(saveTokenNoti(userId));
      }
    });

    OneSignal.on("subscriptionChange", (isSubscribed) => {
      if (isSubscribed) {
        OneSignal.getUserId().then((userId) => {
          console.log("subscriptionChange::installOneSignal 111", userId);
          if (userId) {
            store.dispatch(saveTokenNoti(userId));
          }
        });
      }
    });
  });
};

console.log(process.env);
// Install ServiceWorker and AppCache in the end since
// it's not most important operation and if main code fails,
// we do not want it installed
if (process.env.NODE_ENV === "production") {
  // Init sentry
  Sentry.init({
    dsn: "https://ffd68f22417e4cb7b4aad93094d8fa6a@sentry.io/1473243",
  });
  // require('offline-plugin/runtime').install(); // eslint-disable-line global-require
}
