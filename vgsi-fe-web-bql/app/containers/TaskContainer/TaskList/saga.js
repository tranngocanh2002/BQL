import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { FETCH_ALL_STAFF, FETCH_ALL_TASK } from "./constants";
import {
  fetchAllStaffCompleteAction,
  fetchAllTaskCompleteAction,
} from "./actions";

function* _fetchAllTask(action) {
  try {
    let res = yield window.connection.fetchAllTask({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchAllTaskCompleteAction({
          data: res.data.items,
          totalCount: res.data.pagination ? res.data.pagination.totalCount : 0,
          pageCount: res.data.pagination ? res.data.pagination.pageCount : 0,
        })
      );
    } else {
      yield put(fetchAllTaskCompleteAction());
    }
  } catch (error) {
    yield put(fetchAllTaskCompleteAction());
  }
  // yield put(loginSuccess())
}

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

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_TASK, _fetchAllTask),
    takeLatest(FETCH_ALL_STAFF, _fetchStaff),
  ]);
}
