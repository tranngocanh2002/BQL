import { all, put, takeLatest } from "redux-saga/effects";
import {
  FETCH_ALL_BOOKING_FEE,
  FETCH_APARTMENT,
  FETCH_SERVICE_FREE,
  FETCH_DETAIL_SERVICE
} from "./constants";
import {
  fetchApartmentCompleteAction,
  fetchServiceFreeCompleteAction,
  fetchDetailServiceComplete,
  fetchAllBookingFeeCompleteAction
} from "./actions";

function* _fetchBookingFeeList(action) {
  try {
    let res = yield window.connection.fetchAllServiceBookingFee({
      ...action.payload,
      pageSize: 20
    });
    if (res.success) {
      yield put(
        fetchAllBookingFeeCompleteAction({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount
        })
      );
    } else {
      yield put(fetchAllBookingCompleteAction());
    }
  } catch (error) {
    yield put(fetchAllBookingCompleteAction());
  }
}

function* _fetchAllApartment(action) {
  try {
    let res = yield window.connection.fetchAllApartment({
      ...action.payload,
      pageSize: 20
    });
    if (res.success) {
      yield put(fetchApartmentCompleteAction(res.data.items));
    } else {
      yield put(fetchApartmentCompleteAction());
    }
  } catch (error) {
    yield put(fetchApartmentCompleteAction());
  }
}

function* _fetchAllServiceFree(action, id) {
  try {
    let res = yield window.connection.fetchAllUtilitiIServiceItems({
      ...action.payload,
      pageSize: 2000
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
    takeLatest(FETCH_ALL_BOOKING_FEE, _fetchBookingFeeList),
    takeLatest(FETCH_APARTMENT, _fetchAllApartment),
    takeLatest(FETCH_SERVICE_FREE, _fetchAllServiceFree),
    takeLatest(FETCH_DETAIL_SERVICE, _fetchDetailService),
  ]);
}
