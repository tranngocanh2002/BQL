import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { notificationBar } from "../../../../../utils";
import {
  FETCH_USAGE,
  FETCH_APARTMENT,
  IMPORT_USAGE,
  ADD_INFO,
  UPDATE_INFO,
  DELETE_INFO,
} from "./constants";
import {
  fetchApartmentComplete,
  fetchUsageComplete,
  importUsageComplete,
  addInfoComplete,
  updateInfoComplete,
  deleteInfoComplete,
} from "./actions";
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

function* _fetchUsage(action) {
  try {
    let res = yield window.connection.fetchBuildingInfoUsage({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchUsageComplete({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchUsageComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(fetchUsageComplete());
  }
}
function* _importUsage(action) {
  try {
    let res = yield window.connection.importBuildingInfoUsage({
      ...action.payload,
      is_validate: 0,
    });
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Import data successful.");
      } else {
        notificationBar("Import dữ liệu thành công.");
      }
      yield put(importUsageComplete(true));
    } else {
      yield put(importUsageComplete());
    }
  } catch (error) {}
}
function* _addInfo(action) {
  try {
    let res = yield window.connection.createBuildingInfoUsage(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Create successful.");
      } else {
        notificationBar("Tạo thành công.");
      }
      yield put(addInfoComplete(true));
    } else {
      yield put(addInfoComplete());
    }
  } catch (error) {
    yield put(addInfoComplete());
  }
}

function* _updateInfo(action) {
  try {
    let res = yield window.connection.updateBuildingInfoUsage(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update successful.");
      } else {
        notificationBar("Cập nhật thành công.");
      }
      yield put(updateInfoComplete(true));
    } else {
      yield put(updateInfoComplete());
    }
  } catch (error) {
    yield put(updateInfoComplete());
  }
}
function* _deleteInfo(action) {
  try {
    let res = yield window.connection.deleteBuildingInfoUsage(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete successful.");
      } else {
        notificationBar("Xóa thành công.");
      }
      yield put(deleteInfoComplete(true));
    } else {
      yield put(deleteInfoComplete());
    }
  } catch (error) {
    yield put(deleteInfoComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_APARTMENT, _fetchApartment),
    takeLatest(FETCH_USAGE, _fetchUsage),
    takeLatest(IMPORT_USAGE, _importUsage),
    takeLatest(ADD_INFO, _addInfo),
    takeLatest(UPDATE_INFO, _updateInfo),
    takeLatest(DELETE_INFO, _deleteInfo),
  ]);
}
