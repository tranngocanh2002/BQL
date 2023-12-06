import { take, all, put, select, takeLatest, call } from 'redux-saga/effects';
import { FETCH_ALL_NOTIFICATION_FEE_ACTION } from './constants';
import { fetchNotificationFeeCompleteAction } from './actions';


function* _fetchAllNotificationFee(action) {
  try {
    let res = yield window.connection.fetchAllNotification({ ...action.payload, type_not_in: 0, pageSize: 20 });
    if (res.success) {
      yield put(fetchNotificationFeeCompleteAction({
        data: res.data.items,
        totalPage: res.data.pagination.totalCount
      }));
    } else {
      yield put(fetchNotificationFeeCompleteAction());
    }
  } catch (error) {
    yield put(fetchNotificationFeeCompleteAction())
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_NOTIFICATION_FEE_ACTION, _fetchAllNotificationFee),
  ])
}
