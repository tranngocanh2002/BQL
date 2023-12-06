import {
  take,
  all,
  put,
  select,
  takeLatest,
  call,
  takeEvery,
} from "redux-saga/effects";
import {
  FETCH_APARTMENT,
  CREATE_PAYMENT,
  FETCH_ALL_PAYMENT,
  DELETE_PAYMENT,
  UPDATE_PAYMENT,
  IMPORT_PAYMENT,
  APPROVE_PAYMENT,
  FETCH_LAST_MONTH_FEE,
  FETCH_DESCRIPTION_FEE,
} from "./constants";
import {
  fetchApartmentComplete,
  createPaymentComplete,
  fetchAllPaymentComplete,
  deletePaymentComplete,
  fetchAllPayment,
  updatePaymentComplete,
  importPaymentComplete,
  approvePaymentComplete,
  fetchLastMonthFeeComplete,
  fetchDescriptionFeeComplete,
} from "./actions";
import { notificationBar } from "../../../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchApartment(action) {
  try {
    let res = yield window.connection.fetchAllApartment({
      page: 1,
      pageSize: 20,
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
    const { need_approve, ...rest } = action.payload;
    let res = yield window.connection.createElectricFee(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (need_approve) {
        let resApprove = yield window.connection.approveElectricFee({
          is_active_all: 0,
          is_active_array: [res.data.id],
          service_map_management_id: rest.service_map_management_id,
        });
        if (resApprove.success) {
          if (language === "en") {
            notificationBar("Create payment fee successful.");
          } else {
            notificationBar("Tạo phí thanh toán thành công.");
          }
          yield put(createPaymentComplete(true));
        } else {
          yield put(createPaymentComplete());
        }
        return;
      } else {
        if (language === "en") {
          notificationBar("Create payment fee successful.");
        } else {
          notificationBar("Tạo phí thanh toán thành công.");
        }
        yield put(createPaymentComplete(true));
      }
    } else {
      yield put(createPaymentComplete());
    }
  } catch (error) {
    yield put(createPaymentComplete());
  }
}

function* _updatePayment(action) {
  try {
    const { need_approve, ...rest } = action.payload;
    let res = yield window.connection.updateElectricFee(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (need_approve) {
        let resApprove = yield window.connection.approveElectricFee({
          is_active_all: 0,
          is_active_array: [res.data.id],
          service_map_management_id: rest.service_map_management_id,
        });
        if (resApprove.success) {
          if (language === "en") {
            notificationBar("Update payment fee successful.");
          } else {
            notificationBar("Cập nhật phí thanh toán thành công.");
          }
          yield put(updatePaymentComplete(true));
        } else {
          yield put(updatePaymentComplete());
        }
        return;
      } else {
        if (language === "en") {
          notificationBar("Update payment fee successful.");
        } else {
          notificationBar("Cập nhật phí thanh toán thành công.");
        }
        yield put(updatePaymentComplete(true));
      }
    } else {
      yield put(updatePaymentComplete());
    }
  } catch (error) {
    yield put(updatePaymentComplete());
  }
}
function* _fetchAllPayment(action) {
  try {
    let res = yield window.connection.fetchElectricFee({
      ...action.payload,
      pageSize: 20,
      status: 0,
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
    let res = yield window.connection.deleteElectricFee(rest);
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
    let res = yield window.connection.importFeeElectric({
      ...action.payload,
      is_validate: 0,
    });
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Import data successful.");
      } else {
        notificationBar("Import dữ liệu thành công.");
      }
      yield put(importPaymentComplete(true));
      yield put(fetchAllPayment({ page: 1 }));
    } else {
      yield put(importPaymentComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(importPaymentComplete());
  }
}
function* _fetchLastMonthFee(action) {
  try {
    let res = yield window.connection.fetchLastMonthElectricFee({
      ...action.payload,
    });
    if (res.success) {
      yield put(
        fetchLastMonthFeeComplete({ ...action.payload, data: res.data })
      );
    }
  } catch (error) {
    console.log(error);
  }
}
function* _fetchDescriptionFee(action) {
  try {
    let res = yield window.connection.fetchDescriptionElectricFee({
      ...action.payload,
    });
    if (res.success) {
      yield put(fetchDescriptionFeeComplete(res.data));
    }
  } catch (error) {
    console.log(error);
  }
}
function* _approvePayment(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.approveElectricFee({
      ...rest,
    });
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Approve fee successful.");
      } else {
        notificationBar("Duyệt phí thành công.");
      }
      !!callback && callback();
      yield put(approvePaymentComplete(true));
    } else {
      yield put(approvePaymentComplete(false));
    }
  } catch (error) {
    console.log(error);
    yield put(approvePaymentComplete(false));
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
    takeLatest(APPROVE_PAYMENT, _approvePayment),
    takeEvery(FETCH_LAST_MONTH_FEE, _fetchLastMonthFee),
    takeLatest(FETCH_DESCRIPTION_FEE, _fetchDescriptionFee),
  ]);
}
