import { all, put, select, takeLatest } from "redux-saga/effects";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { notificationBar } from "../../../utils";
import {
  deleteRequestComplete,
  fetchApartmentCompleteAction,
  fetchPaymentRequestComplete,
} from "./actions";
import {
  DELETE_REQUEST,
  FETCH_APARTMENT,
  FETCH_PAYMENT_REQUEST,
} from "./constants";

function* _deleteRequest(action) {
  try {
    let res = yield window.connection.deletePaymentRequest(action.payload);
    let language = yield select(makeSelectLocale());
    action.payload.callback && action.payload.callback();
    if (res.success) {
      if (language === "en") {
        notificationBar("Reject request payment successfully.");
      } else {
        notificationBar("Từ chối yêu cầu thanh toán thành công.");
      }
      yield put(deleteRequestComplete());
    } else {
      yield put(deleteRequestComplete());
    }
  } catch (error) {
    yield put(deleteRequestComplete());
  }
  // yield put(loginSuccess())
}

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
  // yield put(loginSuccess())
}

function* _fetchPaymentRequest(action) {
  try {
    let res = yield window.connection.fetchPaymentRequests({
      ...action.payload,
      pageSize: 20,
      // status: 0,
    });
    if (res.success) {
      yield put(
        fetchPaymentRequestComplete({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchPaymentRequestComplete());
    }
  } catch (error) {
    yield put(fetchPaymentRequestComplete());
  }
}

// Individual exports for testing
export default function* billListSaga() {
  yield all([
    takeLatest(FETCH_APARTMENT, _fetchAllApartment),
    takeLatest(FETCH_PAYMENT_REQUEST, _fetchPaymentRequest),
    takeLatest(DELETE_REQUEST, _deleteRequest),
  ]);
}
