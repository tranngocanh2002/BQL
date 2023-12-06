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
  fetchWaterFeeLevelComplete,
  createWaterFeeLevelComplete,
  fetchWaterFeeLevel,
} from "./actions";
import { notificationBar } from "../../../../../../utils";
import makeSelectMotoPackingServiceContainer from "../selectors";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchFeeLevel(action) {
  try {
    let res = yield window.connection.fetchMotoPackingFeeLevel({
      ...action.payload,
      sort: "code",
      pageSize: 2000,
    });
    if (res.success) {
      yield put(fetchWaterFeeLevelComplete(res.data.items));
    } else {
      yield put(fetchWaterFeeLevelComplete());
    }
  } catch (error) {
    yield put(fetchWaterFeeLevelComplete());
  }
}
function* _createFeeLevel(action) {
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.createMotoPackingFeeLevel(action.payload);
    if (res.success) {
      if (language === "en") {
        notificationBar("Create level fee successful.");
      } else {
        notificationBar("Tạo mức phí thành công.");
      }
      let currentService = yield select(
        makeSelectMotoPackingServiceContainer()
      );
      yield put(
        fetchWaterFeeLevel({
          service_map_management_id: currentService.data.id,
        })
      );
    }
  } catch (error) {}
}
function* _updateFeeLevel(action) {
  let currentService = yield select(makeSelectMotoPackingServiceContainer());
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.updateMotoPackingFeeLevel(action.payload);
    yield put(
      fetchWaterFeeLevel({ service_map_management_id: currentService.data.id })
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
      fetchWaterFeeLevel({ service_map_management_id: currentService.data.id })
    );
  }
}
function* _deleteFeeLevel(action) {
  let currentService = yield select(makeSelectMotoPackingServiceContainer());
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.deleteMotoPackingFeeLevel(action.payload);
    yield put(
      fetchWaterFeeLevel({ service_map_management_id: currentService.data.id })
    );
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete level fee successful.");
      } else {
        notificationBar("Xóa mức phí thành công.");
      }
    }
  } catch (error) {
    yield put(
      fetchWaterFeeLevel({ service_map_management_id: currentService.data.id })
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
