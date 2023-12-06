import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { FETCH_DETAIL_NOTIFICATION, UPDATE_NOTIFICATION } from "./constants";
import {
  fetchDetailNotificationComplete,
  fetchDetailNotification,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchDetailNotification(action) {
  try {
    let res = yield window.connection.fetchDetailNotification(action.payload);
    if (res.success) {
      yield put(fetchDetailNotificationComplete(res.data));
    } else {
    }
  } catch (error) {
    console.log(`error`, error);
  }
}

function* _updateNotification(action) {
  try {
    const { message, ...rest } = action.payload;
    let res = yield window.connection.updateNotification(rest);
    yield put(fetchDetailNotification({ id: action.payload.id }));
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Public notification successful.");
      } else {
        notificationBar("Công khai thông báo thành công.");
      }
    }
  } catch (error) {
    yield fetchDetailNotification({ id: action.payload.id });
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_DETAIL_NOTIFICATION, _fetchDetailNotification),
    takeLatest(UPDATE_NOTIFICATION, _updateNotification),
  ]);
}
