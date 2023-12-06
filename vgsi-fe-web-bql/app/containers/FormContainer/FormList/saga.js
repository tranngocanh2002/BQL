import {
  LOGIN,
  FETCH_ALL_STAFF,
  DELETE_STAFF,
  FETCH_ALL_FORM,
  DELETE_FORM,
  UPDATE_STATUS_FORM,
} from "./constants";

import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  fetchAllFormCompleteAction,
  deleteFormCompleteAction,
  updateFormStatusCompleteAction,
} from "./actions";
import { notification } from "antd";
import { saveToken } from "../../../redux/actions/config";
import { notificationBar, parseTree } from "../../../utils/index";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchForm(action) {
  try {
    let res = yield window.connection.fetchAllForm({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchAllFormCompleteAction({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchAllFormCompleteAction());
    }
  } catch (error) {
    yield put(fetchAllFormCompleteAction());
  }
  // yield put(loginSuccess())
}

function* _deleteForm(action) {
  try {
    const { id, callback } = action.payload;
    let res = yield window.connection.deleteForm({ id });
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete property successful.");
      } else {
        notificationBar("Xóa bất động sản thành công.");
      }
      callback && callback();
    } else {
      yield put(deleteFormCompleteAction());
    }
  } catch (error) {
    yield put(deleteFormCompleteAction());
  }
}

function* _updateStatus(action) {
  try {
    const { id, status, callback, reason } = action.payload;
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
      yield put(updateFormStatusCompleteAction());
      callback && callback();
    } else {
      yield put(updateFormStatusCompleteAction());
    }
  } catch (error) {
    yield put(updateFormStatusCompleteAction());
  }
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(FETCH_ALL_FORM, _fetchForm),
    takeLatest(DELETE_FORM, _deleteForm),
    takeLatest(UPDATE_STATUS_FORM, _updateStatus),
  ]);
}
