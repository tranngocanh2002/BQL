import {
  CREATE_SUPPLIER,
  FETCH_SUPPLIER_DETAIL,
  UPDATE_SUPPLIER,
} from "./constants";

import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  createSupplierCompleteAction,
  fetchSupplierDetailCompleteAction,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _createSupplier(action) {
  try {
    let res = yield window.connection.createContractor(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Add new contractor successful.");
      } else {
        notificationBar("Thêm mới nhà thầu thành công.");
      }
      yield put(createSupplierCompleteAction(true));
    } else {
      yield put(createSupplierCompleteAction());
    }
  } catch (error) {
    yield put(createSupplierCompleteAction());
  }
}

function* _updateSupplier(action) {
  try {
    let res = yield window.connection.updateContractor(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update contractor successful.");
      } else {
        notificationBar("Cập nhật nhà thầu thành công.");
      }
      yield put(createSupplierCompleteAction(true));
    } else {
      yield put(createSupplierCompleteAction());
    }
  } catch (error) {
    yield put(createSupplierCompleteAction());
  }
}

function* _fetchSupplierDetail(action) {
  try {
    let res = yield window.connection.fetchContractorDetail(action.payload);
    if (res.success) {
      yield put(fetchSupplierDetailCompleteAction(res.data));
    } else {
      yield put(fetchSupplierDetailCompleteAction());
    }
  } catch (error) {
    yield put(fetchSupplierDetailCompleteAction());
  }
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(CREATE_SUPPLIER, _createSupplier),
    takeLatest(UPDATE_SUPPLIER, _updateSupplier),
    takeLatest(FETCH_SUPPLIER_DETAIL, _fetchSupplierDetail),
  ]);
}
