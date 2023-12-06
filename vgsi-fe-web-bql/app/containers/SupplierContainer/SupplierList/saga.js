import {
  take,
  all,
  put,
  takeEvery,
  takeLatest,
  call,
  select,
} from "redux-saga/effects";
import { DELETE_SUPPLIER, FETCH_ALL_SUPPLIER } from "./constants";
import {
  deleteSupplierCompleteAction,
  fetchAllSupplierCompleteAction,
} from "./actions";
import { delay } from "redux-saga";
import config from "../../../utils/config";
import { notification } from "antd";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchSupplier(action) {
  try {
    let res = yield window.connection.fetchContractor({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchAllSupplierCompleteAction({
          data: res.data.items.map((mm) => ({
            ...mm,
            type_name: (
              config.TYPE_RESIDENT.find((ii) => ii.id == mm.type) || {}
            ).name,
          })),
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchAllSupplierCompleteAction());
    }
  } catch (error) {
    yield put(fetchAllSupplierCompleteAction());
  }
}

function* _deleteSupplier(action) {
  try {
    const { id, callback } = action.payload;
    let res = yield window.connection.deleteContractor({ id });
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete contractor successful.");
      } else {
        notificationBar("Xóa nhà thầu thành công.");
      }
      callback && callback();
      yield put(deleteSupplierCompleteAction());
    } else {
      yield put(deleteSupplierCompleteAction());
    }
  } catch (error) {
    yield put(deleteSupplierCompleteAction());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_SUPPLIER, _fetchSupplier),
    takeLatest(DELETE_SUPPLIER, _deleteSupplier),
  ]);
}
