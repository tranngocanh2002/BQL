import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { FETCH_ALL_SERVICE_LIST } from "./constants";
import { fetchAllServiceListComplete } from "./actions";

function* _fetchAllServiceList(action) {
  try {
    let res = yield window.connection.fetchAllService({
      page: 1,
      pageSize: 2000,
    });
    if (res.success) {
      yield put(fetchAllServiceListComplete(res.data.items));
    } else {
      yield put(fetchAllServiceListComplete());
    }
  } catch (error) {
    yield put(fetchAllServiceListComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([takeLatest(FETCH_ALL_SERVICE_LIST, _fetchAllServiceList)]);
}
