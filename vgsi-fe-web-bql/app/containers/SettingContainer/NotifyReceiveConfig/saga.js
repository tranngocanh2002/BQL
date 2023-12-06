import {
  FETCH_ALL_NOTIFY_RECEIVE_CONFIG,
  UPDATE_NOTIFY_RECEIVE_CONFIG,
  UPDATE_ALL_NOTIFY_RECEIVE_CONFIG,
} from "./constants";
import { all, put, select, takeLatest } from "redux-saga/effects";
import {
  fetchNotifyReceiveConfigComplete,
  updateNotifyReceiveConfigComplete,
  updateAllNotifyReceiveConfigComplete,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchAllNotifyReceiveConfig() {
  try {
    let res = yield window.connection.fetchAllNotifyReceiveConfig();
    if (res.success) {
      yield put(fetchNotifyReceiveConfigComplete(res.data));
    } else {
      yield put(fetchNotifyReceiveConfigComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(fetchNotifyReceiveConfigComplete());
  }
}
function* _updateNotifyReceiveConfig(action) {
  try {
    let res = yield window.connection.updateNotifyReceiveConfig(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update successful.");
      } else {
        notificationBar("Cập nhật thành công.");
      }
      yield put(updateNotifyReceiveConfigComplete(false));
    } else {
      yield put(updateNotifyReceiveConfigComplete(true));
    }
  } catch (error) {
    console.log(error);
    yield put(updateNotifyReceiveConfigComplete(true));
  }
}

function* _updateAllNotifyReceiveConfig(action) {
  try {
    let res = yield window.connection.updateAllNotifyReceiveConfig(
      action.payload
    );
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update successful.");
      } else {
        notificationBar("Cập nhật thành công.");
      }
      yield put(updateAllNotifyReceiveConfigComplete(false));
    } else {
      yield put(updateAllNotifyReceiveConfigComplete(true));
    }
  } catch (error) {
    console.log(error);
    yield put(updateAllNotifyReceiveConfigComplete(true));
  }
}

// Individual exports for testing
export default function* notifyReceiveConfigSaga() {
  yield all([
    takeLatest(FETCH_ALL_NOTIFY_RECEIVE_CONFIG, _fetchAllNotifyReceiveConfig),
    takeLatest(UPDATE_NOTIFY_RECEIVE_CONFIG, _updateNotifyReceiveConfig),
    takeLatest(UPDATE_ALL_NOTIFY_RECEIVE_CONFIG, _updateAllNotifyReceiveConfig),
  ]);
}
