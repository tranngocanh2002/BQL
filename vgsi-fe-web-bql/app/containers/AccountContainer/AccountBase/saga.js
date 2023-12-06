import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { FETCH_DETAIL, UPDATE_INFO } from "./constants";

import { fetchDetailComplete, updateInfoComplete } from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchDetail(action) {
  try {
    let res = yield window.connection.fetchDetailUser(action.payload);
    if (res.success) {
      yield put(fetchDetailComplete(res.data));
    } else {
      yield put(fetchDetailComplete());
    }
  } catch (error) {
    yield put(fetchDetailComplete());
  }
}

function* _updateInfo(action) {
  try {
    let res = yield window.connection.updateDetailUser(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update information successful.");
      } else {
        notificationBar("Cập nhật thông tin thành công.");
      }
      yield put(updateInfoComplete(res.data));
    } else {
      yield put(updateInfoComplete());
    }
  } catch (error) {
    yield put(updateInfoComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_DETAIL, _fetchDetail),
    takeLatest(UPDATE_INFO, _updateInfo),
  ]);
}
