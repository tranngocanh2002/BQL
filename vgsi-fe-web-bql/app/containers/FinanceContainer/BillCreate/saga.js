// import { take, call, put, select } from 'redux-saga/effects';

import {
  fetchApartmentCompleteAction,
  fetchFilterFeeComplete,
  createBillComplete,
} from "./actions";
import { FETCH_APARTMENT, FETCH_FILTER_FEE, CREATE_BILL } from "./constants";
import { takeLatest, all, put, select } from "redux-saga/effects";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchApartment(action) {
  try {
    let res = yield window.connection.fetchAllApartment({
      ...action.payload,
      pageSize: 200000,
    });
    if (res.success) {
      yield put(fetchApartmentCompleteAction(res.data.items));
    } else {
      yield put(fetchApartmentCompleteAction());
    }
  } catch (error) {
    yield put(fetchApartmentCompleteAction());
  }
  // yield put(loginSuccess())
}

function* _fetchFilterFee(action) {
  try {
    let res = yield window.connection.fetchAllPayment({
      ...action.payload,
      page: 1,
      pageSize: 20,
    });
    if (res.success) {
      yield put(fetchFilterFeeComplete(res.data.items));
    } else {
      yield put(fetchFilterFeeComplete());
    }
  } catch (error) {
    yield put(fetchFilterFeeComplete());
  }
}

function* _createBill(action) {
  const { callback, ...rest } = action.payload;
  try {
    let res = yield window.connection.createBill(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Create bill successful.");
      } else {
        notificationBar("Tạo hóa đơn thành công.");
      }
      yield put(createBillComplete(res.data));
      callback && callback(res.data);
    } else {
      yield put(createBillComplete());
    }
  } catch (error) {
    yield put(createBillComplete());
  }
}

// Individual exports for testing
export default function* billCreateSaga() {
  yield all([
    takeLatest(FETCH_APARTMENT, _fetchApartment),
    takeLatest(FETCH_FILTER_FEE, _fetchFilterFee),
    takeLatest(CREATE_BILL, _createBill),
  ]);
}
