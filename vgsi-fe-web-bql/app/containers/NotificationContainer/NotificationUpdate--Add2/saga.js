import { take, all, put, select, takeLatest, call } from 'redux-saga/effects';
import { notificationBar, parseTree } from "../../../utils";
import { selectBuildingCluster } from '../../../redux/selectors';
import { FETCH_ULTILITY_ACTION, CREATE_NOTIFICATION_ACTION, FETCH_TOTAL_APARTMENT_ACTION, UPDATE_NOTIFICATION_ACTION, FETCH_DETAIL_NOTIFICATION } from './constants';
import { fetchBuildingAreaCompleteAction, fetchCategoryCompleteAction, createNotificationCompleteAction, fetchTotalApartmentCompleteAction, updateNotificationCompleteAction, fetchDetailNotificationComplete } from './actions';


function* _fetchUltility(action) {
  try {
    let res = yield Promise.all([
      window.connection.fetchNotificationCategory({ pageSize: 100000, page: 1 }),
      window.connection.getBuildingArea({ pageSize: 100000, page: 1 })
    ])
    if (res[0].success) {
      yield put(fetchCategoryCompleteAction(res[0].data.items))
    } else {
      yield put(fetchCategoryCompleteAction())
    }
    if (res[1].success) {
      yield put(fetchBuildingAreaCompleteAction(res[1].data.items))
    } else {
      yield put(fetchBuildingAreaCompleteAction())
    }
  } catch (error) {
    yield put(fetchBuildingAreaCompleteAction())
    yield put(fetchCategoryCompleteAction())
  }
}
function* _createNotification(action) {
  try {
    const { message, ...rest } = action.payload
    let res = yield window.connection.createNotification(rest)
    if (res.success) {
      notificationBar(message)
      yield put(createNotificationCompleteAction(true))
    } else {
      yield put(createNotificationCompleteAction())
    }
  } catch (error) {
    yield put(createNotificationCompleteAction())
  }
}
function* _updateNotification(action) {
  try {
    const { message, ...rest } = action.payload
    let res = yield window.connection.updateNotification(rest)
    if (res.success) {
      notificationBar(message)
      yield put(updateNotificationCompleteAction(true))
    } else {
      yield put(updateNotificationCompleteAction())
    }
  } catch (error) {
    yield put(updateNotificationCompleteAction())
  }
}
function* _fetchTotalApartment(action) {
  try {
    if (action.payload.ids.length == 0) {
      yield put(fetchTotalApartmentCompleteAction())
    } else {
      let res = yield window.connection.fetchTotalApartment(action.payload)
      if (res.success) {
        yield put(fetchTotalApartmentCompleteAction(res.data))
      } else {
        yield put(fetchTotalApartmentCompleteAction())
      }
    }
  } catch (error) {
    yield put(fetchTotalApartmentCompleteAction())
  }
}
function* _fetchDetailNotification(action) {
  try {
    let res = yield window.connection.fetchDetailNotification(action.payload)
    if (res.success) {
      yield put(fetchDetailNotificationComplete(res.data))
    } else {
    }
  } catch (error) {
  }
}


// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ULTILITY_ACTION, _fetchUltility),
    takeLatest(CREATE_NOTIFICATION_ACTION, _createNotification),
    takeLatest(FETCH_TOTAL_APARTMENT_ACTION, _fetchTotalApartment),
    takeLatest(UPDATE_NOTIFICATION_ACTION, _updateNotification),
    takeLatest(FETCH_DETAIL_NOTIFICATION, _fetchDetailNotification),
  ])
}
