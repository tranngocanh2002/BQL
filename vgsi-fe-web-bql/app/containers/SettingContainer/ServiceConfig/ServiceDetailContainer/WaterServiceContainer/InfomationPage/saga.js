import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_SERVICE_PROVIDER,
  UPDATE_SERVICE_DETAIL,
  FETCH_WATER_CONFIG,
  UPDATE_WATER_CONFIG,
} from "./constants";
import {
  fetchServiceProviderComplete,
  updateServiceDetailComplete,
  fetchWaterConfigComplete,
  updateWaterConfig,
} from "./actions";
import { notificationBar } from "../../../../../../utils";
import makeSelectWaterServiceContainer from "../selectors";
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
function* _fetchWaterConfig(action) {
  try {
    let currentService = yield select(makeSelectWaterServiceContainer());
    let res = yield window.connection.fetchWaterConfig({
      service_map_management_id: currentService.data.id,
    });
    if (res.success) {
      if (!res.data) {
        yield put(
          updateWaterConfig({
            service_map_management_id: currentService.data.id,
            type: 0,
          })
        );
      } else yield put(fetchWaterConfigComplete(res.data));
    } else {
      yield put(fetchWaterConfigComplete());
    }
  } catch (error) {
    yield put(fetchWaterConfigComplete());
  }
}
function* _updateWaterConfig(action) {
  try {
    let res = yield window.connection.updateWaterConfig(action.payload);
    if (res.success) {
      // notificationBar('Cập nhật thông tin dịch vụ thành công.')
      yield put(fetchWaterConfigComplete(res.data));
    } else {
      yield put(fetchWaterConfigComplete());
    }
  } catch (error) {
    yield put(fetchWaterConfigComplete());
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
    takeLatest(FETCH_WATER_CONFIG, _fetchWaterConfig),
    takeLatest(UPDATE_WATER_CONFIG, _updateWaterConfig),
  ]);
}
