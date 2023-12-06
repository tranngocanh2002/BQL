import { take, all, put, select, takeLatest, call } from 'redux-saga/effects';
import { CREATE_PROVIDER, FETCH_ALL_SERVICE_CLOUD } from './constants';
import { fetchAllServiceCloudComplete } from './actions';

function* _fetchAllServiceCloud(action) {
  try {
    let res = yield window.connection.fetchAllServiceCloud(action.payload)
    if (res.success) {
      yield put(fetchAllServiceCloudComplete(res.data.items))
    } else {
      yield put(fetchAllServiceCloudComplete())
    }
  } catch (error) {
    yield put(fetchAllServiceCloudComplete())
  }
}


// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_SERVICE_CLOUD, _fetchAllServiceCloud),
  ])
}
