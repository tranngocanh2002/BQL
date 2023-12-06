import { all, put, takeLatest } from "redux-saga/effects";
import {
  FETCH_APARTMENT,
  FETCH_SERVICE_FREE,
  FETCH_DETAIL_SERVICE,
  FETCH_SLOT_FREE,
  CREATE_BOOKING,
  FETCH_ALL_CONFIG,
  CHECK_PRICE_BOOKING,
} from "./constants";
import {
  fetchApartmentCompleteAction,
  fetchServiceFreeCompleteAction,
  fetchDetailServiceComplete,
  createBookingComplete,
  fetchSlotFreeComplete,
  fetchAllConfigComplete,
  checkPriceBookingComplete,
} from "./actions";
import { notificationBar } from "../../../utils";

function* _fetchAllApartment(action) {
  try {
    let res = yield window.connection.fetchAllApartment({
      ...action.payload,
      pageSize: 20,
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

function* _fetchDetailService(action) {
  try {
    let res = yield window.connection.fetchAllService({
      page: 1,
      pageSize: 2000,
      service_base_url: action.payload,
    });
    if (res.success && res.data.items.length == 1) {
      ``;
      yield put(fetchDetailServiceComplete(res.data.items[0]));
    } else {
      yield put(fetchDetailServiceComplete());
    }
  } catch (error) {
    yield put(fetchDetailServiceComplete());
  }
}

function* _fetchAllConfig(action) {
  try {
    let res = yield window.connection.fetchAllConfigUtilityServiceItem({
      pageSize: 1000,
      service_utility_free_id: action.payload,
    });
    if (res.success) {
      yield put(fetchAllConfigComplete(res.data.items));
    } else {
      yield put(fetchAllConfigComplete());
    }
  } catch (error) {
    yield put(fetchAllConfigComplete());
  }
}

function* _createBooking(action) {
  try {
    let res = yield window.connection.createBookingUtility(action.payload);
    if (res.success) {
      notificationBar("Tạo đặt chỗ thành công.");
      yield put(createBookingComplete({ status: true, data: res.data }));
    } else {
      yield put(createBookingComplete());
    }
  } catch (error) {
    yield put(createBookingComplete());
  }
}

function* _fetchSlotFree(action) {
  try {
    let res = yield window.connection.fetchConfigPrice({
      ...action.payload,
      pageSize: 1000,
    });
    if (res.success) {
      yield put(
        fetchSlotFreeComplete({ ...action.payload, items: res.data.items })
      );
    }
  } catch (error) {}
}

function* _checkPriceBooking(action) {
  try {
    let res = yield window.connection.checkPriceBookingUtility({
      ...action.payload,
    });
    if (res.success) {
      yield put(checkPriceBookingComplete(res.data));
    } else {
      yield put(checkPriceBookingComplete());
    }
  } catch (error) {
    yield put(checkPriceBookingComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_APARTMENT, _fetchAllApartment),
    takeLatest(FETCH_SERVICE_FREE, _fetchAllServiceFree),
    takeLatest(FETCH_DETAIL_SERVICE, _fetchDetailService),
    takeLatest(FETCH_ALL_CONFIG, _fetchAllConfig),
    takeLatest(CREATE_BOOKING, _createBooking),
    takeLatest(FETCH_SLOT_FREE, _fetchSlotFree),
    takeLatest(CHECK_PRICE_BOOKING, _checkPriceBooking),
  ]);
}
