import { take, all, put, select, takeLatest, call } from 'redux-saga/effects';
import { FETCH_DETAIL_SERVICE } from './constants';
import { fetchDetailServiceComplete } from './actions';


function* _fetchDetailService(action) {
  try {
    let res = yield window.connection.fetchAllService({ page: 1, pageSize: 2000, service_base_url: action.payload });
    if (res.success && res.data.items.length == 1) {``
      yield put(fetchDetailServiceComplete(res.data.items[0]));
    } else {
      yield put(fetchDetailServiceComplete());
    }
  } catch (error) {
    yield put(fetchDetailServiceComplete())
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_DETAIL_SERVICE, _fetchDetailService),
  ])
}
