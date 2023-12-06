import {
  take,
  all,
  put,
  takeEvery,
  takeLatest,
  call,
} from "redux-saga/effects";
import {
  FETCH_ACTION_CONTROLER,
  FETCH_USER_MANAGEMENT,
  FETCH_LOGS,
} from "./constants";
import {
  fetchActionControllerComplete,
  fetchUserManagementComplete,
  fetchLogsComplete,
} from "./actions";

function* _fetchActionController(action) {
  try {
    let res = yield window.connection.fetchActionControllerLog();
    if (res.success) {
      yield put(fetchActionControllerComplete(res.data));
    } else {
      yield put(fetchActionControllerComplete());
    }
  } catch (error) {
    yield put(fetchActionControllerComplete());
  }
}

function* _fetchAllRoles(action) {
  try {
    let res = yield window.connection.getGroupAuth();
    if (res.success) {
      yield put(fetchUserManagementComplete(res.data));
    } else {
      yield put(fetchUserManagementComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(fetchUserManagementComplete());
  }
}

function* _fetchLogs(action) {
  try {
    let res = yield window.connection.fetchLogs({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchLogsComplete({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchLogsComplete());
    }
  } catch (error) {
    yield put(fetchLogsComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ACTION_CONTROLER, _fetchActionController),
    takeLatest(FETCH_USER_MANAGEMENT, _fetchAllRoles),
    takeLatest(FETCH_LOGS, _fetchLogs),
  ]);
}
