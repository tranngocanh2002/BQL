export const INITED = "actions/INITED";
export const SAVE_TOKEN = "actions/SAVE_TOKEN";
export const LOGOUT_ACTION = "actions/LOGOUT_ACTION";
export const LOGOUT_SUCCESS = "actions/LOGOUT_SUCCESS";

export const RESTORE_BUILDING_CLUSTER = "actions/RESTORE_BUILDING_CLUSTER";
export const FETCH_BUILDING_CLUSTER = "actions/FETCH_BUILDING_CLUSTER";

export const FETCH_BUILDING_CLUSTER_COMPLETE =
  "actions/FETCH_BUILDING_CLUSTER_COMPLETE";
export const EDIT_BUILDING_CLUSTER = "actions/EDIT_BUILDING_CLUSTER";

export const INIT_ONE_SIGNAL = "actions/INIT_ONE_SIGNAL";

export const FETCH_CITY = "actions/FETCH_CITY";
export const FETCH_CITY_COMPLETE = "actions/FETCH_CITY_COMPLETE";

// export const FETCH_WEATHER_CURRENT = 'actions/FETCH_WEATHER_CURRENT'
// export const FETCH_WEATHER_CURRENT_COMPLETE = 'actions/FETCH_WEATHER_CURRENT_COMPLETE'

export const SAVE_TOKEN_NOTI = "actions/SAVE_TOKEN_NOTI";

export const ADD_NOTIFICATION_PULL = "actions/ADD_NOTIFICATION_PULL";
export const CLEAR_ALL_NOTIFICATION = "actions/CLEAR_ALL_NOTIFICATION";

export function inited() {
  return {
    type: INITED,
  };
}
export function saveToken(payload) {
  return {
    type: SAVE_TOKEN,
    payload,
  };
}
export function clearAllNotification(payload) {
  return {
    type: CLEAR_ALL_NOTIFICATION,
    payload,
  };
}
export function addNotification(payload) {
  return {
    type: ADD_NOTIFICATION_PULL,
    payload,
  };
}
export function logout(payload) {
  return {
    type: LOGOUT_ACTION,
    payload,
  };
}
export function logoutSuccess(payload) {
  return {
    type: LOGOUT_SUCCESS,
    payload,
  };
}
export function fetchBuildingCluster(payload) {
  return {
    type: FETCH_BUILDING_CLUSTER,
    payload,
  };
}
export function fetchBuildingClusterComplete(payload) {
  return {
    type: FETCH_BUILDING_CLUSTER_COMPLETE,
    payload,
  };
}
// export function fetchWeatherCurrent(payload) {
//   return {
//     type: FETCH_WEATHER_CURRENT,
//     payload
//   }
// }
// export function fetchWeatherCurrentComplete(payload) {
//   return {
//     type: FETCH_WEATHER_CURRENT_COMPLETE,
//     payload
//   }
// }
export function restoreBuildingCluster(payload) {
  return {
    type: RESTORE_BUILDING_CLUSTER,
    payload,
  };
}
export function editBuildingCluster(payload) {
  return {
    type: EDIT_BUILDING_CLUSTER,
    payload,
  };
}
export function fetchCity(payload) {
  return {
    type: FETCH_CITY,
    payload,
  };
}
export function fetchCityComplete(payload) {
  return {
    type: FETCH_CITY_COMPLETE,
    payload,
  };
}
export function saveTokenNoti(payload) {
  return {
    type: SAVE_TOKEN_NOTI,
    payload,
  };
}

export function initOneSignal(payload) {
  return {
    type: INIT_ONE_SIGNAL,
    payload,
  };
}
