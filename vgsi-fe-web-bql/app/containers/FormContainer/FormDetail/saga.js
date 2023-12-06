import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { FETCH_DETAIL_FORM, UPDATE_DETAIL } from "./constants";
import {
  fetchDetailFormCompleteAction,
  updateDetailCompleteAction,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchDetail(action) {
  try {
    let res = yield window.connection.fetchDetailForm(action.payload);
    if (res.success) {
      yield put(fetchDetailFormCompleteAction(res.data));
    } else {
      yield put(fetchDetailFormCompleteAction());
    }
  } catch (error) {
    yield put(fetchDetailFormCompleteAction());
  }
}

function* _updateDetail(action) {
  try {
    const { id, status, reason, callback, ...rest } = action.payload;
    let res = yield window.connection.updateStatusForm({ id, status, reason });
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        if (reason) {
          notificationBar("Reject form successfully.");
        } else {
          notificationBar("Form approval successful.");
        }
      } else {
        if (reason) {
          notificationBar("Từ chối đăng ký biểu mẫu thành công.");
        } else {
          notificationBar("Phê duyệt đăng ký biểu mẫu thành công.");
        }
      }
      callback && callback();
    } else {
      yield put(updateDetailCompleteAction());
    }
  } catch (error) {
    yield put(updateDetailCompleteAction());
  }
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(FETCH_DETAIL_FORM, _fetchDetail),
    takeLatest(UPDATE_DETAIL, _updateDetail),
  ]);
}
