import { all, put, takeLatest } from 'redux-saga/effects';
import { FETCH_ALL_SERVICE } from './constants';
import { fetchAllServiceComplete } from './actions';


function* _fetchAllService(action) {
  try {
    let res = yield window.connection.fetchAllService({ page: 1, pageSize: 2000 });
    if (res.success) {
      yield put(fetchAllServiceComplete(res.data.items));
    } else {
      yield put(fetchAllServiceComplete());
    }
  } catch (error) {
    yield put(fetchAllServiceComplete())
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_SERVICE, _fetchAllService),
  ])
}
