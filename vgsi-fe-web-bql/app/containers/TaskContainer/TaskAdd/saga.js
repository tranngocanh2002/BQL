import { CREATE_TASK, FETCH_ALL_STAFF } from "./constants";

import { all, put, takeLatest } from "redux-saga/effects";
import { fetchAllStaffCompleteAction, createTaskComplete } from "./actions";
import { notificationBar } from "../../../utils";

function* _fetchStaff(action) {
  try {
    let res = yield window.connection.fetchStaff({
      ...action.payload,
      pageSize: 66666,
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

function* _createTask(action) {
  try {
    let res = yield window.connection.createTask(action.payload);
    if (res.success) {
      yield put(createTaskComplete(true));
    } else {
      yield put(createTaskComplete(false));
    }
  } catch (error) {
    yield put(createTaskComplete(false));
  }
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(FETCH_ALL_STAFF, _fetchStaff),
    takeLatest(CREATE_TASK, _createTask),
  ]);
}
