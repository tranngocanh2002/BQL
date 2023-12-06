import { all, put, takeLatest } from 'redux-saga/effects';
import { FETCH_DETAIL_OLD_RESIDENT } from './constants';
import { fetchDetailOldResidentCompleteAction } from './actions';

function* _fetchDetailOldResident(action) {
  try {
    let res = yield window.connection.fetchDetailOldResident(action.payload)
    if (res.success) {
      yield put(fetchDetailOldResidentCompleteAction(res.data))
    } else {
      yield put(fetchDetailOldResidentCompleteAction())
    }
  } catch (error) {
    console.log(error);
    yield put(fetchDetailOldResidentCompleteAction())
  }
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(FETCH_DETAIL_OLD_RESIDENT, _fetchDetailOldResident),
  ])
}