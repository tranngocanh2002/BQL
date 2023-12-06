import { FETCH_ALL_STAFF, FETCH_DETAIL_TASK, UPDATE_TASK } from "./constants";

import { all, put, takeLatest } from "redux-saga/effects";
import {
  fetchAllStaffCompleteAction,
  fetchDetailTaskCompleteAction,
  updateTaskComplete,
} from "./actions";

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

function* _fetchTask(action) {
  try {
    let res = yield window.connection.fetchDetailTask(action.payload);
    if (res.success) {
      yield put(fetchDetailTaskCompleteAction(res.data));
    } else {
      yield put(fetchDetailTaskCompleteAction());
    }
  } catch (error) {
    yield put(fetchDetailTaskCompleteAction());
  }
}

function* _updateTask(action) {
  try {
    let res = yield window.connection.updateTask(action.payload);
    if (res.success) {
      yield put(updateTaskComplete(true));
    } else {
      yield put(updateTaskComplete(false));
    }
  } catch (error) {
    yield put(updateTaskComplete(false));
  }
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(FETCH_ALL_STAFF, _fetchStaff),
    takeLatest(FETCH_DETAIL_TASK, _fetchTask),
    takeLatest(UPDATE_TASK, _updateTask),
  ]);
}
