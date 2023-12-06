import { take, call, put, select, all, takeLatest } from "redux-saga/effects";
import {
  FETCH_ALL_FEE,
  FETCH_SERVICE_MAP,
  DELETE_FEE,
  UPDATE_PAYMENT,
} from "./constants";
import {
  fetchAllFeeComplete,
  fetchServiceMapCompleteAction,
  deleteFeeCompleteAction,
  updatePaymentComplete,
} from "./actions";
import { notificationBar } from "../../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchAllFee(action) {
  try {
    let res = yield window.connection.fetchAllPayment({
      ...action.payload,
      is_draft: 0,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchAllFeeComplete({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchAllFeeComplete());
    }
  } catch (error) {
    yield put(fetchAllFeeComplete());
  }
}

function* _fetchAllServiceMap(action) {
  try {
    let res = yield window.connection.fetchAllService({
      ...action.payload,
      pageSize: 200000,
    });
    if (res.success) {
      yield put(fetchServiceMapCompleteAction(res.data.items));
    } else {
      yield put(fetchServiceMapCompleteAction());
    }
  } catch (error) {
    yield put(fetchServiceMapCompleteAction());
  }
  // yield put(loginSuccess())
}

function* _deleteFee(action) {
  const { callback, ...rest } = action.payload;
  try {
    let res = yield window.connection.deletePayment(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete payment fee successful.");
      } else {
        notificationBar("Xóa phí thanh toán thành công.");
      }
      callback && callback();
      yield put(deleteFeeCompleteAction(true));
    } else {
      yield put(deleteFeeCompleteAction(false));
    }
  } catch (error) {
    yield put(deleteFeeCompleteAction(false));
  }
  // yield put(loginSuccess())
}

function* _updatePayment(action) {
  try {
    let res = yield window.connection.updatePayment(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update payment fee successful.");
      } else {
        notificationBar("Cập nhật phí thanh toán thành công.");
      }
      yield put(updatePaymentComplete(true));
    } else {
      yield put(updatePaymentComplete(false));
    }
  } catch (error) {
    yield put(updatePaymentComplete(false));
  }
}

// Individual exports for testing
export default function* feeListSaga() {
  yield all([
    takeLatest(FETCH_ALL_FEE, _fetchAllFee),
    takeLatest(FETCH_SERVICE_MAP, _fetchAllServiceMap),
    takeLatest(DELETE_FEE, _deleteFee),
    takeLatest(UPDATE_PAYMENT, _updatePayment),
  ]);
}
