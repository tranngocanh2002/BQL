import {
  FETCH_ALL_NOTIFY_SEND_CONFIG,
  UPDATE_NOTIFY_SEND_CONFIG,
  UPDATE_ALL_NOTIFY_SEND_CONFIG,
} from "./constants";
import { all, put, select, takeLatest } from "redux-saga/effects";
import {
  fetchNotifySendConfigComplete,
  updateNotifySendConfigComplete,
  updateAllNotifySendConfigComplete,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchAllNotifySendConfig() {
  try {
    let res = yield window.connection.fetchAllNotifySendConfig();
    if (res.success) {
      yield put(fetchNotifySendConfigComplete(res.data));
    } else {
      yield put(fetchNotifySendConfigComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(fetchNotifySendConfigComplete());
  }
}
function* _updateNotifySendConfig(action) {
  try {
    let res = yield window.connection.updateNotifySendConfig(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update successful.");
      } else {
        notificationBar("Cập nhật thành công.");
      }
      yield put(updateNotifySendConfigComplete(false));
    } else {
      yield put(updateNotifySendConfigComplete(true));
    }
  } catch (error) {
    console.log(error);
    yield put(updateNotifySendConfigComplete(true));
  }
}
function* _updateAllNotifySendConfig(action) {
  try {
    let res = yield window.connection.updateAllNotifySendConfig(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update successful.");
      } else {
        notificationBar("Cập nhật thành công.");
      }
      yield put(updateAllNotifySendConfigComplete(false));
    } else {
      yield put(updateAllNotifySendConfigComplete(true));
    }
  } catch (error) {
    console.log(error);
    yield put(updateAllNotifySendConfigComplete(true));
  }
}

// Individual exports for testing
export default function* notifySendConfigSaga() {
  yield all([
    takeLatest(FETCH_ALL_NOTIFY_SEND_CONFIG, _fetchAllNotifySendConfig),
    takeLatest(UPDATE_NOTIFY_SEND_CONFIG, _updateNotifySendConfig),
    takeLatest(UPDATE_ALL_NOTIFY_SEND_CONFIG, _updateAllNotifySendConfig),
  ]);
}
