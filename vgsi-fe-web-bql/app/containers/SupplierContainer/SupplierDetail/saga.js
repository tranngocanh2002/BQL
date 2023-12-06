import {
  take,
  all,
  put,
  takeEvery,
  takeLatest,
  call,
} from "redux-saga/effects";
import { delay } from "redux-saga";
import config from "../../../utils/config";
import { notification } from "antd";
import { notificationBar } from "../../../utils";
import { fetchSupplierDetailCompleteAction } from "./actions";
import { FETCH_SUPPLIER_DETAIL } from "./constants";

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
export default function* rootSaga() {
  yield all([takeLatest(FETCH_SUPPLIER_DETAIL, _fetchSupplierDetail)]);
}
