import { all, put, select, takeLatest } from "redux-saga/effects";
import {
  FETCH_SERVICE_PROVIDER,
  UPDATE_SERVICE_DETAIL,
  FETCH_OLD_DEBIT_CONFIG,
  UPDATE_OLD_DEBIT_CONFIG,
} from "./constants";
import {
  fetchServiceProviderComplete,
  updateServiceDetailComplete,
  fetchOldDebitConfigComplete,
  updateOldDebitConfig,
} from "./actions";
import { notificationBar } from "../../../../../../utils";
import makeSelectOldDebitServiceContainer from "../selectors";
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
function* _fetchOldDebitConfig(action) {
  try {
    let currentService = yield select(makeSelectOldDebitServiceContainer());
    let res = yield window.connection.fetchOldDebitConfig({
      service_map_management_id: currentService.data.id,
    });
    if (res.success) {
      if (!res.data) {
        yield put(
          updateOldDebitConfig({
            service_map_management_id: currentService.data.id,
            type: 0,
          })
        );
      } else yield put(fetchOldDebitConfigComplete(res.data));
    } else {
      yield put(fetchOldDebitConfigComplete());
    }
  } catch (error) {
    yield put(fetchOldDebitConfigComplete());
  }
}
function* _updateOldDebitConfig(action) {
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.updateOldDebitConfig(action.payload);
    if (res.success) {
      if (language === "en") {
        notificationBar("Update service information successful.");
      } else {
        notificationBar("Cập nhật thông tin dịch vụ thành công.");
      }
      yield put(fetchOldDebitConfigComplete(res.data));
    } else {
      yield put(fetchOldDebitConfigComplete());
    }
  } catch (error) {
    yield put(fetchOldDebitConfigComplete());
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
    takeLatest(FETCH_OLD_DEBIT_CONFIG, _fetchOldDebitConfig),
    takeLatest(UPDATE_OLD_DEBIT_CONFIG, _updateOldDebitConfig),
  ]);
}
