import {
  LOGIN,
  FETCH_ALL_STAFF,
  DELETE_STAFF,
  FETCH_GROUP_AUTH,
  IMPORT_STAFF,
  CHANGE_STATUS_STAFF,
} from "./constants";

import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  loginSuccess,
  loginFailed,
  fetchAllStaffCompleteAction,
  deleteStaffCompleteAction,
  fetchGroupAuthCompleteAction,
  importStaffAction,
  importStaffCompleteAction,
} from "./actions";
import { notification } from "antd";
import { saveToken } from "../../../redux/actions/config";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { changeStatusStaffCompleteAction } from "./actions";

function* _fetchStaff(action) {
  try {
    let res = yield window.connection.fetchStaff({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchAllStaffCompleteAction({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchAllStaffCompleteAction());
    }
  } catch (error) {
    yield put(fetchAllStaffCompleteAction());
  }
  // yield put(loginSuccess())
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

function* _importStaff(action) {
  try {
    let res = yield window.connection.importUser(action.payload);
    if (res.success) {
      yield put(importStaffCompleteAction(true));
    } else {
      yield put(importStaffCompleteAction());
    }
  } catch (error) {
    yield put(importStaffCompleteAction());
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
          notificationBar("Deactivate successfully.");
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
// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(FETCH_GROUP_AUTH, _fetchAuthGroup),
    takeLatest(FETCH_ALL_STAFF, _fetchStaff),
    takeLatest(DELETE_STAFF, _deleteStaff),
    takeLatest(IMPORT_STAFF, _importStaff),
    takeLatest(CHANGE_STATUS_STAFF, _changeStatusStaff),
  ]);
}
