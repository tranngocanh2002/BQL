import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_DETAIL_SERVICE,
  FETCH_APARTMENT,
  CREATE_PAYMENT,
  FETCH_ALL_PAYMENT,
  DELETE_PAYMENT,
  UPDATE_PAYMENT,
  IMPORT_PAYMENT,
} from "./constants";
import {
  fetchDetailServiceComplete,
  fetchApartmentComplete,
  createPaymentComplete,
  fetchAllPaymentComplete,
  deletePaymentComplete,
  fetchAllPayment,
  updatePaymentComplete,
  importPaymentComplete,
} from "./actions";
import makeSelectMotoPackingServiceContainer, {
  selectMotoPackingServiceContainerDomain,
} from "../selectors";
import { notificationBar } from "../../../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchApartment(action) {
  try {
    let res = yield window.connection.fetchAllApartment({
      page: 1,
      pageSize: 2000,
      ...action.payload,
    });
    if (res.success) {
      yield put(fetchApartmentComplete(res.data.items));
    } else {
      yield put(fetchApartmentComplete());
    }
  } catch (error) {
    yield put(fetchApartmentComplete());
  }
}

function* _createPayment(action) {
  try {
    let res = yield window.connection.createPayment(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Create payment fee successful.");
      } else {
        notificationBar("Tạo phí thanh toán thành công.");
      }
      yield put(createPaymentComplete(true));
    } else {
      yield put(createPaymentComplete());
    }
  } catch (error) {
    yield put(createPaymentComplete());
  }
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
      yield put(updatePaymentComplete());
    }
  } catch (error) {
    yield put(updatePaymentComplete());
  }
}
function* _fetchAllPayment(action) {
  try {
    let currentService = yield select(makeSelectMotoPackingServiceContainer());
    let res = yield window.connection.fetchAllPayment({
      ...action.payload,
      pageSize: 20,
      service_map_management_id: currentService.data.id,
      is_draft: 0,
    });
    if (res.success) {
      yield put(
        fetchAllPaymentComplete({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchAllPaymentComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(fetchAllPaymentComplete());
  }
}
function* _deletePayment(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.deletePayment(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete payment fee successful.");
      } else {
        notificationBar("Xóa phí thanh toán thành công.");
      }
      callback && callback();
    } else {
      yield put(deletePaymentComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(deletePaymentComplete());
  }
}
function* _importPayment(action) {
  try {
    let res = yield window.connection.importPayment({
      file_path: action.payload,
    });
    if (res.success) {
      yield put(importPaymentComplete(true));
    } else {
      yield put(importPaymentComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(importPaymentComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_APARTMENT, _fetchApartment),
    takeLatest(CREATE_PAYMENT, _createPayment),
    takeLatest(FETCH_ALL_PAYMENT, _fetchAllPayment),
    takeLatest(DELETE_PAYMENT, _deletePayment),
    takeLatest(UPDATE_PAYMENT, _updatePayment),
    takeLatest(IMPORT_PAYMENT, _importPayment),
  ]);
}
