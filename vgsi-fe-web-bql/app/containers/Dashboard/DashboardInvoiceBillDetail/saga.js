import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_DETAIL_BILL,
  CANCEL_BILL,
  CHANGE_STATUS_BILL,
  UPDATE_BILL,
  BLOCK_BILL,
} from "./constants";
import {
  fetchDetailBillComplete,
  cancelBillComplete,
  changeStatusBillComplete,
  updateBillComplete,
  blockBillComplete,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchBillDetail(action) {
  try {
    let res = yield window.connection.fetchDetailBill(action.payload);
    if (res.success) {
      yield put(fetchDetailBillComplete(res.data));
    } else {
      yield put(fetchDetailBillComplete());
    }
  } catch (error) {
    yield put(fetchDetailBillComplete());
  }
}

function* _cancelBill(action) {
  try {
    let res = yield window.connection.cancelBill(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Cancel payment voucher successful.");
      } else {
        notificationBar("Hủy phiếu chi thành công.");
      }
      yield put(cancelBillComplete(true));
    } else {
      yield put(cancelBillComplete());
    }
  } catch (error) {
    yield put(cancelBillComplete());
  }
}

function* _changeStatusBill(action) {
  try {
    let res = yield window.connection.updateStatusBill(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar(
          `${
            action.payload.status == 1 ? "Journal entry" : "Closing entry"
          } thành công.`
        );
      } else {
        notificationBar(
          `${action.payload.status == 1 ? "Mở khoá" : "Chốt sổ"} thành công.`
        );
      }
      yield put(changeStatusBillComplete(action.payload));
    } else {
      yield put(changeStatusBillComplete());
    }
  } catch (error) {
    yield put(changeStatusBillComplete());
  }
}

function* _updateBill(action) {
  try {
    let res = yield window.connection.updateInvoiceBill(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update payment voucher successful.");
      } else {
        notificationBar("Cập nhật phiếu chi thành công.");
      }
      yield put(updateBillComplete(res.data));
    } else {
      yield put(updateBillComplete());
    }
  } catch (error) {
    yield put(updateBillComplete());
  }
}

function* _blockBill(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.blockBill(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Closing entry successful.");
      } else {
        notificationBar("Chốt sổ thành công.");
      }
      yield put(blockBillComplete());
      callback && callback();
    } else {
      yield put(blockBillComplete());
    }
  } catch (error) {
    yield put(blockBillComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_DETAIL_BILL, _fetchBillDetail),
    takeLatest(CANCEL_BILL, _cancelBill),
    takeLatest(CHANGE_STATUS_BILL, _changeStatusBill),
    takeLatest(UPDATE_BILL, _updateBill),
    takeLatest(BLOCK_BILL, _blockBill),
  ]);
}
