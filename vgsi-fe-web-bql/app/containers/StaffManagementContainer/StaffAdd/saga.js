import {
  LOGIN,
  FETCH_GROUP_AUTH,
  CREATE_STAFF,
  UPDATE_STAFF,
  FETCH_DETAIL,
  UPDATE_STAFF_AND_USERDETAIL,
} from "./constants";

import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  loginSuccess,
  loginFailed,
  fetchGroupAuthCompleteAction,
  createStaffCompleteAction,
  updateStaffCompleteAction,
  fetchDetailCompleteAction,
  updateStaffAndDetailComplete,
} from "./actions";
import { notification } from "antd";
import { saveToken } from "../../../redux/actions/config";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchAuthGroup(action) {
  try {
    let res = yield window.connection.getGroupAuth();
    if (res.success) {
      yield put(fetchGroupAuthCompleteAction(res.data));
    } else {
      yield put(fetchGroupAuthCompleteAction());
    }
  } catch (error) {
    yield put(fetchGroupAuthCompleteAction());
  }
  // yield put(loginSuccess())
}

function* _createStaff(action) {
  try {
    let res = yield window.connection.createStaff(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Add staff successful.");
      } else {
        notificationBar("Thêm nhân sự thành công.");
      }
      yield put(createStaffCompleteAction(true));
    } else {
      yield put(createStaffCompleteAction());
    }
  } catch (error) {
    yield put(createStaffCompleteAction());
  }
  // yield put(loginSuccess())
}
function* _updateStaff(action) {
  try {
    let res = yield window.connection.updateStaff(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update staff successful.");
      } else {
        notificationBar("Cập nhật nhân sự thành công.");
      }
      yield put(updateStaffCompleteAction(true));
    } else {
      yield put(updateStaffCompleteAction());
    }
  } catch (error) {
    yield put(updateStaffCompleteAction());
  }
  // yield put(loginSuccess())
}
function* _updateStaffAndDetail(action) {
  try {
    let res = yield window.connection.updateStaff(action.payload);
    const language = yield select(makeSelectLocale());

    if (res.success) {
      if (language === "en") {
        notificationBar("Update staff successful.");
      } else {
        notificationBar("Cập nhật nhân sự thành công.");
      }
      yield put(updateStaffAndDetailComplete(res.data));
    } else {
      yield put(updateStaffAndDetailComplete());
    }
  } catch (error) {
    yield put(updateStaffAndDetailComplete());
  }
  // yield put(loginSuccess())
}
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

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(FETCH_GROUP_AUTH, _fetchAuthGroup),
    takeLatest(CREATE_STAFF, _createStaff),
    takeLatest(UPDATE_STAFF, _updateStaff),
    takeLatest(UPDATE_STAFF_AND_USERDETAIL, _updateStaffAndDetail),
    takeLatest(FETCH_DETAIL, _fetchDetail),
  ]);
}
