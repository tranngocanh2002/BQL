import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_ALL_VEHICLE,
  FETCH_APARTMENT,
  FETCH_ALL_FEE_LEVEL,
  CREATE_VEHICLE,
  DELETE_VEHICLE,
  UPDATE_VEHICLE,
  IMPORT_VEHICLE,
  ACTIVE_VEHICLE,
  CANCEL_VEHICLE,
} from "./constants";
import {
  fetchAllVehicleComplete,
  fetchApartmentComplete,
  fetchAllFeeLevelComplete,
  createVehicleComplete,
  deleteVehicleComplete,
  updateVehicleComplete,
  importVehicleComplete,
  activeVehicleComplete,
  cancelVehicleComplete,
} from "./actions";
import makeSelectMotoPackingServiceContainer from "../selectors";
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

function* _fetchAllVehicle(action) {
  try {
    let currentService = yield select(makeSelectMotoPackingServiceContainer());
    let res = yield window.connection.fetchAllVehicle({
      ...action.payload,
      pageSize: 20,
      service_map_management_id: currentService.data.id,
    });
    if (res.success) {
      yield put(
        fetchAllVehicleComplete({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchAllVehicleComplete());
    }
  } catch (error) {
    console.log(`error`, error);
    yield put(fetchAllVehicleComplete());
  }
}

function* _fetchFeeLevel(action) {
  try {
    let currentService = yield select(makeSelectMotoPackingServiceContainer());
    let res = yield window.connection.fetchMotoPackingFeeLevel({
      service_map_management_id: currentService.data.id,
      pageSize: 2000,
    });
    if (res.success) {
      yield put(fetchAllFeeLevelComplete(res.data.items));
    } else {
      yield put(fetchAllFeeLevelComplete());
    }
  } catch (error) {
    yield put(fetchAllFeeLevelComplete());
  }
}

function* _createVehicle(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.createVehicle(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Add new vehicle successful.");
      } else {
        notificationBar("Thêm mới xe thành công.");
      }
      yield put(
        createVehicleComplete({
          success: true,
        })
      );
    } else {
      yield put(createVehicleComplete({ success: false }));
    }
  } catch (error) {
    yield put(createVehicleComplete({ success: false }));
  }
}

function* _deleteVehicle(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.deleteVehicle(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete vehicle successful.");
      } else {
        notificationBar("Xóa xe thành công.");
      }
      yield put(deleteVehicleComplete(true));
    } else {
      yield put(deleteVehicleComplete());
    }
  } catch (error) {
    yield put(deleteVehicleComplete());
  }
}

function* _updateVehicle(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.updateVehicle(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update vehicle successful.");
      } else {
        notificationBar("Cập nhật xe thành công.");
      }
      yield put(
        updateVehicleComplete({
          success: true,
        })
      );
    } else {
      yield put(updateVehicleComplete({ success: false }));
    }
  } catch (error) {
    yield put(updateVehicleComplete({ success: false }));
  }
}

function* _importVehicle(action) {
  try {
    let currentService = yield select(makeSelectMotoPackingServiceContainer());
    let res = yield window.connection.importVehicle({
      file_path: action.payload,
      service_map_management_id: currentService.data.id,
      is_validate: 0,
    });
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Import data successful.");
      } else {
        notificationBar("Import dữ liệu thành công.");
      }
      yield put(importVehicleComplete(true));
    } else {
      yield put(importVehicleComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(importVehicleComplete());
  }
}

function* _activeVehicle(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.activeVehicle(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Activate vehicle successful.");
      } else {
        notificationBar("Kích hoạt xe thành công.");
      }
      yield put(
        activeVehicleComplete({
          success: true,
        })
      );
    } else {
      yield put(activeVehicleComplete({ success: false }));
    }
  } catch (error) {
    yield put(activeVehicleComplete({ success: false }));
  }
}

function* _cancelVehicle(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.cancelVehicle(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar(
          res.message ? res.message : "Deactivate vehicle successful."
        );
      } else {
        notificationBar(
          res.message ? res.message : "Hủy kích hoạt xe thành công."
        );
      }
      yield put(
        cancelVehicleComplete({
          success: true,
        })
      );
    } else {
      yield put(cancelVehicleComplete({ success: false }));
    }
  } catch (error) {
    yield put(cancelVehicleComplete({ success: false }));
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_VEHICLE, _fetchAllVehicle),
    takeLatest(FETCH_APARTMENT, _fetchApartment),
    takeLatest(FETCH_ALL_FEE_LEVEL, _fetchFeeLevel),
    takeLatest(CREATE_VEHICLE, _createVehicle),
    takeLatest(DELETE_VEHICLE, _deleteVehicle),
    takeLatest(UPDATE_VEHICLE, _updateVehicle),
    takeLatest(IMPORT_VEHICLE, _importVehicle),
    takeLatest(ACTIVE_VEHICLE, _activeVehicle),
    takeLatest(CANCEL_VEHICLE, _cancelVehicle),
  ]);
}
