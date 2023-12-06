import { take, all, put, takeEvery, takeLatest, call } from 'redux-saga/effects';
import { FETCH_LOG_EMAIL, FETCH_LOG_SMS, FETCH_LOG_NOTIFICATION } from './constants';
import { fetchLogEmailComplete, fetchLogSMSComplete, fetchLogNotificationComplete } from './actions';


function* _fetchLogEmail(action) {
  try {
    let res = yield window.connection.buildingClusterListSend({
      ...action.payload,
      type_send: 1,
      pageSize: 20
    })
    if (res.success) {
      yield put(fetchLogEmailComplete(res.data))
    } else {
      yield put(fetchLogEmailComplete())
    }
  } catch (error) {
    yield put(fetchLogEmailComplete())
  }
}


function* _fetchLogSMS(action) {
  try {
    let res = yield window.connection.buildingClusterListSend({
      ...action.payload,
      type_send: 2,
      pageSize: 20
    })
    if (res.success) {
      yield put(fetchLogSMSComplete(res.data))
    } else {
      yield put(fetchLogSMSComplete())
    }
  } catch (error) {
    yield put(fetchLogSMSComplete())
  }
}


function* _fetchLogNotification(action) {
  try {
    let res = yield window.connection.buildingClusterListSend({
      ...action.payload,
      type_send: 3,
      pageSize: 20
    })
    if (res.success) {
      yield put(fetchLogNotificationComplete(res.data))
    } else {
      yield put(fetchLogNotificationComplete())
    }
  } catch (error) {
    yield put(fetchLogNotificationComplete())
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_LOG_EMAIL, _fetchLogEmail),
    takeLatest(FETCH_LOG_SMS, _fetchLogSMS),
    takeLatest(FETCH_LOG_NOTIFICATION, _fetchLogNotification),
  ])
}
