import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_SERVICE_PROVIDER,
  UPDATE_SERVICE_DETAIL,
  FETCH_FEE_LEVEL,
  CREATE_FEE_LEVEL,
  DELETE_FEE_LEVEL,
  UPDATE_FEE_LEVEL,
} from "./constants";
import {
  fetchServiceProviderComplete,
  updateServiceDetailComplete,
  fetchElectricFeeLevelComplete,
  createElectricFeeLevelComplete,
  fetchElectricFeeLevel,
} from "./actions";
import { notificationBar } from "../../../../../../utils";
import makeSelectElectricServiceContainer from "../selectors";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchFeeLevel(action) {
  try {
    let res = yield window.connection.fetchElectricFeeLevel({
      ...action.payload,
      pageSize: 2000,
    });
    if (res.success) {
      res.data.items.sort((f1, f2) => f1.from_level - f2.from_level);
      yield put(fetchElectricFeeLevelComplete(res.data.items));
    } else {
      yield put(fetchElectricFeeLevelComplete());
    }
  } catch (error) {
    console.log(`error`, error);
    yield put(fetchElectricFeeLevelComplete());
  }
}
function* _createFeeLevel(action) {
  try {
    let res = yield window.connection.createElectricFeeLevel(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Create level fee successful.");
      } else {
        notificationBar("Tạo mức phí thành công.");
      }
      let currentService = yield select(makeSelectElectricServiceContainer());
      yield put(
        fetchElectricFeeLevel({
          service_map_management_id: currentService.data.id,
        })
      );
    }
  } catch (error) {}
}
function* _updateFeeLevel(action) {
  let currentService = yield select(makeSelectElectricServiceContainer());
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.updateElectricFeeLevel(action.payload);
    yield put(
      fetchElectricFeeLevel({
        service_map_management_id: currentService.data.id,
      })
    );
    if (res.success) {
      if (action.payload.isNoti)
        if (language === "en") {
          notificationBar("Update level fee successful.");
        } else {
          notificationBar("Cập nhật mức phí thành công.");
        }
    }
  } catch (error) {
    yield put(
      fetchElectricFeeLevel({
        service_map_management_id: currentService.data.id,
      })
    );
  }
}
function* _deleteFeeLevel(action) {
  let currentService = yield select(makeSelectElectricServiceContainer());
  const { callback, ...rest } = action.payload;
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.deleteElectricFeeLevel(rest.record);
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete level fee successful.");
      } else {
        notificationBar("Xóa mức phí thành công.");
      }
      !!callback && callback();
      yield put(
        fetchElectricFeeLevel({
          service_map_management_id: currentService.data.id,
        })
      );
    }
  } catch (error) {
    yield put(
      fetchElectricFeeLevel({
        service_map_management_id: currentService.data.id,
      })
    );
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    // takeLatest(FETCH_SERVICE_PROVIDER, _fetchServiceProvider),
    takeLatest(FETCH_FEE_LEVEL, _fetchFeeLevel),
    takeLatest(CREATE_FEE_LEVEL, _createFeeLevel),
    takeLatest(DELETE_FEE_LEVEL, _deleteFeeLevel),
    takeLatest(UPDATE_FEE_LEVEL, _updateFeeLevel),
  ]);
}
