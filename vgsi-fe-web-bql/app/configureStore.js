/**
 * Create the store with dynamic reducers
 */

import { routerMiddleware } from "connected-react-router/immutable";
import { fromJS } from "immutable";
import jwtDecode from "jwt-decode";
import localForage from "localforage";
import { applyMiddleware, compose, createStore } from "redux";
import { autoRehydrate, persistStore } from "redux-persist-immutable";
import createSagaMiddleware from "redux-saga";
import Connection from "./connection";
import createReducer from "./reducers";
import {
  fetchBuildingCluster,
  inited,
  logoutSuccess,
  restoreBuildingCluster,
  saveToken,
  saveTokenNoti,
} from "./redux/actions/config";
import { fetchCountUnreadNotification } from "./redux/actions/notification";
import rootSaga from "./redux/saga";

const sagaMiddleware = createSagaMiddleware();

export default function configureStore(initialState = {}, history) {
  // Create the store with two middlewares
  // 1. sagaMiddleware: Makes redux-sagas work
  // 2. routerMiddleware: Syncs the location/URL path to the state
  const middlewares = [sagaMiddleware, routerMiddleware(history)];

  const enhancers = [applyMiddleware(...middlewares), autoRehydrate()];

  // If Redux DevTools Extension is installed use it, otherwise use Redux compose
  /* eslint-disable no-underscore-dangle, indent */
  const composeEnhancers =
    process.env.NODE_ENV !== "production" &&
    typeof window === "object" &&
    window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__
      ? window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__({})
      : compose;
  /* eslint-enable */

  const store = createStore(
    createReducer(),
    fromJS(initialState),
    composeEnhancers(...enhancers)
  );

  persistStore(
    store,
    {
      storage: localForage,
      whitelist: ["config", "language"],
    },
    async () => {
      console.log("Redux-Persist loaded state", store.getState().toJS());
      Connection.init(store);
      store.dispatch(
        restoreBuildingCluster(
          store.getState().get("config").toJS().buildingCluster
        )
      );
      store.dispatch(fetchBuildingCluster(window.location.hostname));

      const token = store.getState().get("config").get("token");
      const refresh_token = store.getState().get("config").get("refresh_token");
      const pathname = history.location.pathname;
      console.log("pathname", pathname);

      if (token) {
        try {
          const tokenDecode = jwtDecode(token);
          if (
            Math.floor(new Date().getTime() / 1000) + 4 * 60 * 60 >=
            tokenDecode.exp
          ) {
            window.connection.refreshNewToken(refresh_token).then((res) => {
              if (res.success) {
                const tokenNoti = store
                  .getState()
                  .get("config")
                  .get("tokenNoti");
                if (tokenNoti) {
                  store.dispatch(saveTokenNoti(tokenNoti));
                }

                //Fetch count unread lan dau
                store.dispatch(fetchCountUnreadNotification());

                if (pathname == "/" || pathname == "/user/login") {
                  history.push("/main/home");
                }
              }
            });
          } else {
            const auth_group = store.getState().get("config").toJS().auth_group;
            const tokenNoti = store.getState().get("config").get("tokenNoti");
            const info_user = store.getState().get("config").toJS().info_user;
            store.dispatch(
              saveToken({
                access_token: token,
                auth_group,
                info_user,
                refresh_token,
              })
            );
            if (tokenNoti) {
              store.dispatch(saveTokenNoti(tokenNoti));
            }

            //Fetch count unread lan dau
            store.dispatch(fetchCountUnreadNotification());

            if (pathname == "/" || pathname == "/user/login") {
              history.push("/main/home");
            }
          }
        } catch (error) {
          console.log("error", error);
          store.dispatch(logoutSuccess());
        }
      } else {
        if (pathname == "/") {
          history.push("/user/login");
        }
      }
      store.dispatch(inited());
      // store.dispatch(fetchWeatherCurrent())
    }
  );

  // Extensions
  store.runSaga = sagaMiddleware.run;
  store.runSaga(rootSaga);
  store.injectedReducers = {}; // Reducer registry
  store.injectedSagas = {}; // Saga registry

  // Make reducers hot reloadable, see http://mxs.is/googmo
  /* istanbul ignore next */
  if (module.hot) {
    module.hot.accept("./reducers", () => {
      store.replaceReducer(createReducer(store.injectedReducers));
    });
  }

  return store;
}
