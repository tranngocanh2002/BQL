// Individual exports for testing
import { all, put, select, takeLatest } from "redux-saga/effects";
import {
  FETCH_DETAIL_BILL,
  DELETE_BILL,
  UPDATE_BILL,
  UPDATE_BILL_STATUS,
} from "./constants";
import config from "../../../utils/config";
import {
  fetchDetailBillCompleteAction,
  deleteDetailBillCompleteAction,
  updateDetailBillCompleteAction,
  updateStatusBillCompleteAction,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchBill(action) {
  try {
    let res = yield window.connection.fetchDetailBill(action.payload);
    if (res.success) {
      yield put(fetchDetailBillCompleteAction(res.data));
    } else {
      yield put(fetchDetailBillCompleteAction());
    }
  } catch (error) {
    yield put(fetchDetailBillCompleteAction());
  }
  // yield put(loginSuccess())
}

function* _deleteBill(action) {
  try {
    let res = yield window.connection.deleteBill(action.payload);
    if (res.success) {
      yield put(deleteDetailBillCompleteAction(true));
    } else {
      yield put(deleteDetailBillCompleteAction(false));
    }
  } catch (error) {
    yield put(deleteDetailBillCompleteAction(false));
  }
  // yield put(loginSuccess())
}

function* _updateBill(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.updateBill(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update bill successful.");
      } else {
        notificationBar("Cập nhật hóa đơn thành công.");
      }
      callback && callback();
      yield put(updateDetailBillCompleteAction(true));
    } else {
      yield put(updateDetailBillCompleteAction(false));
    }
  } catch (error) {
    yield put(updateDetailBillCompleteAction(false));
  }
  // yield put(loginSuccess())
}

function* _updateStatusBill(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.updateStatusBill(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update status receipt successful.");
      } else {
        notificationBar("Cập nhật trạng thái phiếu thu thành công.");
      }
      callback && callback();
      yield put(updateStatusBillCompleteAction(true));
    } else {
      yield put(updateStatusBillCompleteAction(false));
    }
  } catch (error) {
    yield put(updateStatusBillCompleteAction(false));
  }
}

export default function* invoiceBillDetailSaga() {
  yield all([
    takeLatest(DELETE_BILL, _deleteBill),
    takeLatest(FETCH_DETAIL_BILL, _fetchBill),
    takeLatest(UPDATE_BILL, _updateBill),
    takeLatest(UPDATE_BILL_STATUS, _updateStatusBill),
  ]);
}
