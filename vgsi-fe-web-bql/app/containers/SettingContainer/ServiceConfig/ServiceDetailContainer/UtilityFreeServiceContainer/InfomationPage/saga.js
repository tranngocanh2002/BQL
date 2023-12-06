import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { FETCH_SERVICE_PROVIDER, UPDATE_SERVICE_DETAIL } from "./constants";
import {
  fetchServiceProviderComplete,
  updateServiceDetailComplete,
} from "./actions";
import { notificationBar } from "../../../../../../utils";
import { fetchAllServiceList } from "../../../ServiceList/actions";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
function* _fetchServiceProvider(action) {
  try {
    let res = yield window.connection.fetchServiceProvider({
      ...action.payload,
      pageSize: 2000,
    });
    if (res.success) {
      yield put(fetchServiceProviderComplete(res.data.items));
    } else {
      yield put(fetchServiceProviderComplete());
    }
  } catch (error) {
    yield put(fetchServiceProviderComplete());
  }
}
function* _updateServiceDetail(action) {
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.updateDetailService(action.payload);
    if (res.success) {
      if (language === "en") {
        notificationBar("Update service information successful.");
      } else {
        notificationBar("Cập nhật thông tin dịch vụ thành công.");
      }
      yield put(updateServiceDetailComplete(res.data));
      yield put(fetchAllServiceList());
    } else {
      yield put(updateServiceDetailComplete());
    }
  } catch (error) {
    yield put(updateServiceDetailComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_SERVICE_PROVIDER, _fetchServiceProvider),
    takeLatest(UPDATE_SERVICE_DETAIL, _updateServiceDetail),
  ]);
}
