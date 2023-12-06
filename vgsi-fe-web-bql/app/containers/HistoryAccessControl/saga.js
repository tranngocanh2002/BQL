import { take, all, put, takeEvery, takeLatest, call } from 'redux-saga/effects';
import { FETCH_ALL_APARTMENT, FETCH_ALL_HISTORY } from './constants';
import { fetchVehicleComplete, fetchApartmentComplete, fetchAllHistoryComplete } from './actions';
import { delay } from 'redux-saga'

function* _fetchAllHistory(action) {
  try {
    let res = yield window.connection.identifyHistory({ ...action.payload, pageSize: 20 })
    if (res.success) {
      yield put(fetchAllHistoryComplete({
        data: res.data.items,
        totalPage: res.data.pagination.totalCount
      }))
    } else {
      yield put(fetchAllHistoryComplete())
    }
  } catch (error) {
    yield put(fetchAllHistoryComplete())
  }
}


function* _fetchAllApartment(action) {
  try {
    let res = yield window.connection.fetchAllApartment({ ...action.payload, pageSize: 200000 })
    if (res.success) {
      yield put(fetchApartmentComplete(res.data.items))
    } else {
      yield put(fetchApartmentComplete())
    }
  } catch (error) {
    yield put(fetchApartmentComplete())
  }
  // yield put(loginSuccess())
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_HISTORY, _fetchAllHistory),
    takeLatest(FETCH_ALL_APARTMENT, _fetchAllApartment),
  ])
}
