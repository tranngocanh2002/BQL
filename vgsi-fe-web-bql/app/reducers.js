/**
 * Combine all reducers in this file and export the combined reducers.
 */

import { combineReducers } from "redux-immutable";
import { connectRouter } from "connected-react-router/immutable";
import _ from "lodash";
import history from "utils/history";
import languageProviderReducer from "containers/LanguageProvider/reducer";
import { fromJS } from "immutable";

import {
  INITED,
  SAVE_TOKEN,
  LOGOUT_SUCCESS,
  FETCH_BUILDING_CLUSTER,
  FETCH_BUILDING_CLUSTER_COMPLETE,
  RESTORE_BUILDING_CLUSTER,
  EDIT_BUILDING_CLUSTER,
  FETCH_CITY,
  FETCH_CITY_COMPLETE,
  SAVE_TOKEN_NOTI,
  ADD_NOTIFICATION_PULL,
  CLEAR_ALL_NOTIFICATION,
} from "./redux/actions/config";
import jwtDecode from "jwt-decode";
import {
  FETCH_ALL_NOTIFICATION,
  FETCH_ALL_NOTIFICATION_COMPLETE,
  FETCH_COUNT_UNREAD_COMPLETE,
  SEEN_NOTIFICATION_COMPLETE,
} from "./redux/actions/notification";
import {
  FETCH_DETAIL,
  FETCH_DETAIL_COMPLETE,
  UPDATE_INFO_COMPLETE,
} from "./containers/AccountContainer/AccountBase/constants";
import { UPDATE_STAFF_AND_USERDETAIL_COMPLETE } from "./containers/StaffManagementContainer/StaffAdd/constants";

const initialState = fromJS({
  inited: false,
  token: undefined,
  buildingCluster: {
    loading: true,
    editting: false,
    data: undefined,
  },
  city: {
    loading: false,
    lst: [],
  },
  userDetail: undefined,
  notifications: {
    loading: false,
    totalPage: 1,
    page: 1,
    data: [],
    totalUnread: 0,
  },
});

function initial(state = initialState, action) {
  switch (action.type) {
    case INITED:
      return state.merge({
        inited: true,
      });
    case ADD_NOTIFICATION_PULL: {
      return state.set(
        "notifications",
        state.get("notifications").concat(fromJS([action.payload]))
      );
    }
    case CLEAR_ALL_NOTIFICATION: {
      return state.set("notifications", fromJS([]));
    }
    case SAVE_TOKEN: {
      const { access_token, auth_group, info_user, refresh_token } =
        action.payload;
      let userDetail = info_user;
      let newAuthGroup = { ...(auth_group || {}) };
      newAuthGroup.checkRole = function (roles) {
        if (!roles || roles.length == 0 || !this.data_role) {
          return false;
        }
        if (
          _.xor(this.data_role, roles).length ==
          this.data_role.length + roles.length
        ) {
          return false;
        }
        return true;
      };
      return state
        .set("token", access_token)
        .set("refresh_token", refresh_token)
        .set("auth_group", newAuthGroup)
        .set("userDetail", userDetail ? fromJS(userDetail) : undefined);
    }
    case FETCH_DETAIL_COMPLETE:
    case UPDATE_INFO_COMPLETE: {
      if (!action.payload) {
        return state;
      }
      const { auth_group, ...rest } = action.payload;
      return state.set(
        "userDetail",
        fromJS({ ...(state.toJS().userDetail || {}), ...rest })
      );
    }
    case UPDATE_STAFF_AND_USERDETAIL_COMPLETE: {
      if (!action.payload) {
        return state;
      }
      const { auth_group, ...rest } = action.payload;
      return state.set(
        "userDetail",
        fromJS({ ...(state.toJS().userDetail || {}), ...rest })
      );
    }
    case LOGOUT_SUCCESS: {
      return state.set("token", undefined).set("auth_group", undefined);
    }
    case FETCH_CITY:
      return state.setIn(["city", "loading"], true);
    case FETCH_CITY_COMPLETE:
      return state
        .setIn(["city", "loading"], false)
        .setIn(["city", "lst"], fromJS(action.payload || []));
    case FETCH_BUILDING_CLUSTER: {
      return state.setIn(["buildingCluster", "loading"], true);
    }
    case EDIT_BUILDING_CLUSTER: {
      return state.setIn(["buildingCluster", "editting"], true);
    }
    case FETCH_BUILDING_CLUSTER_COMPLETE: {
      return state
        .setIn(["buildingCluster", "loading"], false)
        .setIn(["buildingCluster", "editting"], false)
        .setIn(
          ["buildingCluster", "data"],
          action.payload ? fromJS(action.payload) : undefined
        );
    }
    case RESTORE_BUILDING_CLUSTER: {
      return state
        .setIn(["buildingCluster", "loading"], false)
        .setIn(
          ["buildingCluster", "data"],
          action.payload ? fromJS(action.payload) : undefined
        );
    }
    case FETCH_ALL_NOTIFICATION: {
      return state.setIn(["notifications", "loading"], true);
    }
    case FETCH_COUNT_UNREAD_COMPLETE: {
      return state.setIn(["notifications", "totalUnread"], action.payload);
    }
    case SEEN_NOTIFICATION_COMPLETE: {
      return state.setIn(
        ["notifications", "data"],
        state
          .get("notifications")
          .toJS()
          .data.map((ddd) => {
            if (
              ddd.id == action.payload ||
              action.payload == undefined ||
              action.payload == -1
            ) {
              return {
                ...ddd,
                is_read: 1,
              };
            }
            return ddd;
          })
      );
    }
    case FETCH_ALL_NOTIFICATION_COMPLETE: {
      let data = [];
      let totalPage = 1;
      let page = 1;
      let totalUnread = state.get("notifications").toJS().totalUnread;

      if (action.payload) {
        page = action.payload.page;
        data =
          page == 1
            ? action.payload.data
            : state
                .get("notifications")
                .toJS()
                .data.concat(action.payload.data);
        totalPage = action.payload.totalPage;
        if (totalPage == undefined) {
          totalPage = state.get("notifications").toJS().totalPage;
        }

        if (action.payload.total_unread != undefined) {
          totalUnread = action.payload.total_unread;
        }
      }

      return state
        .setIn(["notifications", "loading"], false)
        .setIn(["notifications", "data"], data)
        .setIn(["notifications", "totalPage"], totalPage)
        .setIn(["notifications", "page"], page)
        .setIn(["notifications", "totalUnread"], totalUnread);
    }
    default:
      return state;
  }
}

const initialStateConfig = fromJS({
  token: undefined,
  auth_group: undefined,
  buildingCluster: undefined,
  tokenNoti: undefined,
  environment: {
    // temperature: 28,
    // uv: 9,
    // windSpeed: 4,
    // windDirection: 'Đông',
    // rainFall: 0,
    // humidity: 30,
  },
});

function config(state = initialStateConfig, action) {
  switch (action.type) {
    // case FETCH_WEATHER_CURRENT_COMPLETE: {
    //   return state.set('environment', action.payload)
    // }
    case SAVE_TOKEN: {
      const { access_token, auth_group, info_user, refresh_token } =
        action.payload;
      return state
        .set("token", access_token)
        .set("refresh_token", refresh_token)
        .set("auth_group", fromJS(auth_group))
        .set("info_user", fromJS(info_user));
    }
    case FETCH_DETAIL_COMPLETE:
    case UPDATE_INFO_COMPLETE: {
      if (!action.payload) {
        return state;
      }
      return state.set("info_user", fromJS(action.payload));
    }
    case LOGOUT_SUCCESS: {
      return state
        .set("token", undefined)
        .set("refresh_token", undefined)
        .set("auth_group", undefined);
    }
    case FETCH_BUILDING_CLUSTER_COMPLETE: {
      return state.set(
        "buildingCluster",
        action.payload ? fromJS(action.payload) : undefined
      );
    }
    case SAVE_TOKEN_NOTI: {
      return state.set("tokenNoti", action.payload);
    }
    default:
      return state;
  }
}

/**
 * Merges the main reducer with the router state and dynamically injected reducers
 */
export default function createReducer(injectedReducers = {}) {
  const rootReducer = combineReducers({
    language: languageProviderReducer,
    webState: initial,
    config,
    ...injectedReducers,
  });

  // Wrap the root reducer and return a new root reducer with router state
  const mergeWithRouterState = connectRouter(history);
  return mergeWithRouterState(rootReducer);
}
