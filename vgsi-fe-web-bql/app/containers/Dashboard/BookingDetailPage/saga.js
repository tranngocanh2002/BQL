// Individual exports for testing
import { all, put, takeLatest } from "redux-saga/effects";
import { FETCH_DETAIL_BOOKING, FETCH_SERVICE_FREE } from "./constants";
import {
  fetchDetailBookingCompleteAction,
  fetchServiceFreeCompleteAction,
} from "./actions";

function* _fetchBooking(action) {
  try {
    let res = yield window.connection.fetchDetailBookingUtility(action.payload);
    if (res.success) {
      yield put(fetchDetailBookingCompleteAction(res.data));
    } else {
      yield put(fetchDetailBookingCompleteAction());
    }
  } catch (error) {
    yield put(fetchDetailBookingCompleteAction());
  }
}
function* _fetchAllServiceFree(action, id) {
  try {
    let res = yield window.connection.fetchAllUtilitiIServiceItems({
      ...action.payload,
      pageSize: 2000,
    });
    if (res.success) {
      yield put(fetchServiceFreeCompleteAction(res.data.items));
    } else {
      yield put(fetchServiceFreeCompleteAction());
    }
  } catch (error) {
    yield put(fetchServiceFreeCompleteAction());
  }
}

export default function* bookingDetailSaga() {
  yield all([
    takeLatest(FETCH_DETAIL_BOOKING, _fetchBooking),
    takeLatest(FETCH_SERVICE_FREE, _fetchAllServiceFree),
  ]);
}
