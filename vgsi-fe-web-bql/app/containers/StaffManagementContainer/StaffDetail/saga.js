import {
  CHANGE_STATUS_STAFF,
  DELETE_STAFF,
  FETCH_DETAIL_STAFF,
  RESET_PASSWORD_STAFF,
} from "./constants";

import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  changeStatusStaffCompleteAction,
  deleteStaffCompleteAction,
  fetchDetailCompleteAction,
  resetPasswordCompleteAction,
} from "./actions";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { notificationBar } from "../../../utils";

function* _fetchDetail(action) {
  try {
    let res = yield window.connection.fetchDetailStaff(action.payload);
    if (res.success) {
      yield put(fetchDetailCompleteAction(res.data));
    } else {
      yield put(fetchDetailCompleteAction());
    }
  } catch (error) {
    yield put(fetchDetailCompleteAction());
  }
  // yield put(loginSuccess())
}

function* _resetPasswordStaff(action) {
  try {
    let res = yield window.connection.resetPasswordUser(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Reset password successfully.");
      } else {
        notificationBar("Đặt lại mật khẩu thành công.");
      }
      yield put(resetPasswordCompleteAction(true));
    } else {
      yield put(resetPasswordCompleteAction());
    }
  } catch (error) {
    yield put(resetPasswordCompleteAction());
  }
}

function* _changeStatusStaff(action) {
  try {
    const { callback } = action.payload;
    let res = yield window.connection.changeStatusStaff(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        if (action.payload.status === 0) {
          notificationBar("Stop activation successfully.");
        } else {
          notificationBar("Activate successfully.");
        }
      } else {
        if (action.payload.status === 0) {
          notificationBar("Dừng kích hoạt thành công.");
        } else {
          notificationBar("Kích hoạt thành công.");
        }
      }
      yield put(changeStatusStaffCompleteAction(true));
      callback && callback();
    } else {
      yield put(changeStatusStaffCompleteAction());
    }
  } catch (error) {
    yield put(changeStatusStaffCompleteAction());
  }
}
function* _deleteStaff(action) {
  try {
    const { id, callback } = action.payload;
    let res = yield window.connection.deleteStaff({ id });
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete staff successfully.");
      } else {
        notificationBar("Xóa nhân sự thành công!");
      }
      callback && callback();
    } else {
      yield put(deleteStaffCompleteAction());
    }
  } catch (error) {
    yield put(deleteStaffCompleteAction());
  }
}
// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(FETCH_DETAIL_STAFF, _fetchDetail),
    takeLatest(RESET_PASSWORD_STAFF, _resetPasswordStaff),
    takeLatest(CHANGE_STATUS_STAFF, _changeStatusStaff),
    takeLatest(DELETE_STAFF, _deleteStaff),
  ]);
}
