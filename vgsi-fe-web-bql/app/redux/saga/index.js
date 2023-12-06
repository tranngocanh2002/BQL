import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { delay } from "redux-saga";
import {
  FETCH_BUILDING_CLUSTER,
  fetchBuildingClusterComplete,
  EDIT_BUILDING_CLUSTER,
  FETCH_CITY,
  fetchCityComplete,
  SAVE_TOKEN_NOTI,
  LOGOUT_ACTION,
  logoutSuccess,
  INIT_ONE_SIGNAL,
  initOneSignal,
  // FETCH_WEATHER_CURRENT,
  // fetchWeatherCurrentComplete,
  // fetchWeatherCurrent
} from "../actions/config";
import { selectToken, selectBuildingCluster } from "../selectors";
import {
  FETCH_ALL_NOTIFICATION,
  fetchAllNotificationComplete,
  fetchCountUnreadNotificationComplete,
  SEEN_NOTIFICATION,
  seenNotificationComplete,
  FETCH_COUNT_UNREAD,
} from "../actions/notification";
import { notificationBar } from "../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
const degToCard = (deg) => {
  if (deg > 11.25 && deg < 33.75) {
    return "NNE";
  } else if (deg > 33.75 && deg < 56.25) {
    return "ENE";
  } else if (deg > 56.25 && deg < 78.75) {
    return "E";
  } else if (deg > 78.75 && deg < 101.25) {
    return "ESE";
  } else if (deg > 101.25 && deg < 123.75) {
    return "ESE";
  } else if (deg > 123.75 && deg < 146.25) {
    return "SE";
  } else if (deg > 146.25 && deg < 168.75) {
    return "SSE";
  } else if (deg > 168.75 && deg < 191.25) {
    return "S";
  } else if (deg > 191.25 && deg < 213.75) {
    return "SSW";
  } else if (deg > 213.75 && deg < 236.25) {
    return "SW";
  } else if (deg > 236.25 && deg < 258.75) {
    return "WSW";
  } else if (deg > 258.75 && deg < 281.25) {
    return "W";
  } else if (deg > 281.25 && deg < 303.75) {
    return "WNW";
  } else if (deg > 303.75 && deg < 326.25) {
    return "NW";
  } else if (deg > 326.25 && deg < 348.75) {
    return "NNW";
  } else {
    return "N";
  }
};

function* _fetchBUildingCluster(action) {
  try {
    let res = yield window.connection.getBuildingClusterDetail({
      domain: action.payload,
    });
    if (res.success) {
      yield put(fetchBuildingClusterComplete(res.data));
      yield put(initOneSignal(res.data));
    } else {
      yield put(fetchBuildingClusterComplete());
    }
  } catch (error) {
    yield put(fetchBuildingClusterComplete());
  }
  // yield put(loginSuccess())
}
function* _editBuildingCluster(action) {
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.updateBuildingCluster(action.payload);
    if (res.success) {
      if (language === "en") {
        notificationBar("Edit building cluster information successful.");
      } else {
        notificationBar("Chỉnh sửa thông tin thành công.");
      }

      yield put(fetchBuildingClusterComplete(res.data));
    } else {
      yield put(fetchBuildingClusterComplete());
    }
  } catch (error) {
    yield put(fetchBuildingClusterComplete());
  }
  // yield put(loginSuccess())
}
function* _fetchCity(action) {
  try {
    let res = yield window.connection.fetchCity();
    if (res.success) {
      yield put(fetchCityComplete(res.data.items));
    } else {
      yield put(fetchCityComplete());
    }
  } catch (error) {
    yield put(fetchCityComplete());
  }
  // yield put(loginSuccess())
}
function* _saveTokenNoti(action) {
  try {
    yield delay(1000);
    let token = yield select(selectToken());
    if (token)
      yield window.connection.createTokenDevice({
        device_token: action.payload,
      });
  } catch (error) {}
  // yield put(loginSuccess())
}
function* _logout(action) {
  try {
    yield window.connection.logout();
    yield put(logoutSuccess());
  } catch (error) {
    yield put(logoutSuccess());
  }
  // yield put(loginSuccess())
}

function* _initOneSignal(action) {
  if (action.payload) {
    try {
      yield window.installOneSignal(action.payload.one_signal_app_id);
    } catch (error) {
      console.log("error", error);
    }
  }
}

function* _fetchCountUnread(action) {
  try {
    let token = yield select(selectToken());
    if (token) {
      let res = yield window.connection.fetchCountUnreadNotification();
      if (res.success) {
        yield put(fetchCountUnreadNotificationComplete(res.data.total_unread));
      }
    }
  } catch (error) {
    console.log("error", error);
  }
}

function* _seenNotification(action) {
  try {
    let payload = {};
    if (action.payload == undefined || action.payload == -1) {
      payload = {
        is_read_all: 1,
      };
    } else {
      payload = {
        is_read_all: 0,
        is_read_array: [action.payload],
      };
    }
    let res = yield window.connection.readNotification(payload);
    if (res.success) {
      yield put(seenNotificationComplete(action.payload));
      yield _fetchCountUnread();
    }
  } catch (error) {
    console.log("error", error);
  }
}

function* _fetchAllNotification(action) {
  const { page } = action.payload;
  try {
    let res = yield window.connection.fetchNotification({ page, pageSize: 20 });
    if (res.success) {
      yield put(
        fetchAllNotificationComplete({
          data: res.data.items,
          totalPage: res.data.pagination.pageCount,
          page,
          total_unread: res.data.total_unread,
        })
      );
    } else {
      yield put(
        fetchAllNotificationComplete({
          data: [],
          page: Math.max(page - 1, 1),
        })
      );
    }
  } catch (error) {
    console.log("error", error);
    yield put(
      fetchAllNotificationComplete({
        data: [],
        page: Math.max(page - 1, 1),
      })
    );
  }
}

// function* _fetchWeatherCurrent(action) {
//     try {

//         let buildingCluster = yield select(selectBuildingCluster())
//         if (!buildingCluster.data) {
//             yield delay(1000)
//             yield put(fetchWeatherCurrent())
//             return
//         }
//         let res = yield fetch(buildingCluster.data.link_whether).then(res => res.json())
//         let { weather, main, wind } = res
//         yield put(fetchWeatherCurrentComplete({
//             temperature: main.temp,
//             windSpeed: wind.speed,
//             windDirection: degToCard(wind.deg),
//             rainFall: main.rain || 0,
//             humidity: main.humidity,
//             weatherCurrent: weather[0].description,
//             icon: weather[0].icon,
//             uv: 2,
//             timestamp: Date.now()
//         }))
//     } catch (e) {
//     } finally {
//         yield delay(5 * 60 * 1000)
//         yield put(fetchWeatherCurrent())
//     }
// }

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(FETCH_BUILDING_CLUSTER, _fetchBUildingCluster),
    takeLatest(EDIT_BUILDING_CLUSTER, _editBuildingCluster),
    takeLatest(INIT_ONE_SIGNAL, _initOneSignal),
    takeLatest(FETCH_CITY, _fetchCity),
    takeLatest(SAVE_TOKEN_NOTI, _saveTokenNoti),
    takeLatest(LOGOUT_ACTION, _logout),
    takeLatest(FETCH_ALL_NOTIFICATION, _fetchAllNotification),
    takeLatest("@@router/LOCATION_CHANGE", _fetchCountUnread),
    takeLatest(FETCH_COUNT_UNREAD, _fetchCountUnread),
    takeLatest(SEEN_NOTIFICATION, _seenNotification),
    // takeLatest(FETCH_WEATHER_CURRENT, _fetchWeatherCurrent),
  ]);
}
