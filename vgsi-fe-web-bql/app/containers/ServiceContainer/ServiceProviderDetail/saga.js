import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_DETAIL_SERVICE_PROVIDER,
  UPDATE_SERVICE_PROVIDER,
} from "./constants";
import {
  fetchDetailServiceProviderComplete,
  updateServiceProviderComplete,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchDetailServiceProvider(action) {
  try {
    let res = yield window.connection.fetchDetailServiceProvider(
      action.payload
    );
    if (res.success) {
      yield put(fetchDetailServiceProviderComplete(res.data));
    } else {
    }
  } catch (error) {}
}
function* _updateServiceProvider(action) {
  try {
    let res = yield window.connection.updateServiceProvider(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Edit contractor information successful.");
      } else {
        notificationBar("Chỉnh sửa thông tin nhà cung cấp thành công.");
      }
      yield put(updateServiceProviderComplete(res.data));
    } else {
      yield put(updateServiceProviderComplete());
    }
  } catch (error) {
    yield put(updateServiceProviderComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_DETAIL_SERVICE_PROVIDER, _fetchDetailServiceProvider),
    takeLatest(UPDATE_SERVICE_PROVIDER, _updateServiceProvider),
  ]);
}
