import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { all, put, select, takeLatest } from "redux-saga/effects";
import { notificationBar } from "../../../../../utils";
import makeSelectUtilityFreeServiceContainer from "../selectors";
import {
  createListUltilityComplete,
  deleteListUltilityComplete,
  fetchAllListUltilityComplete,
  fetchApartmentComplete,
  importListUltilityComplete,
  updateListUltilityComplete,
} from "./actions";
import {
  CREATE_PAYMENT,
  DELETE_PAYMENT,
  FETCH_ALL_ITEMS,
  FETCH_APARTMENT,
  IMPORT_PAYMENT,
  UPDATE_PAYMENT,
} from "./constants";

function* _fetchApartment(action) {
  try {
    let res = yield window.connection.fetchAllApartment({
      page: 1,
      pageSize: 2000,
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

function* _createListUltility(action) {
  try {
    let res = yield window.connection.createListUltility(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Create payment fee successful.");
      } else {
        notificationBar("Tạo phí thanh toán thành công.");
      }
      yield put(createListUltilityComplete(true));
    } else {
      yield put(createListUltilityComplete());
    }
  } catch (error) {
    yield put(createListUltilityComplete());
  }
}

function* _updateListUltility(action) {
  try {
    let res = yield window.connection.updateListUltility(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update payment fee successful.");
      } else {
        notificationBar("Cập nhật phí thanh toán thành công.");
      }
      yield put(updateListUltilityComplete(true));
    } else {
      yield put(updateListUltilityComplete());
    }
  } catch (error) {
    yield put(updateListUltilityComplete());
  }
}
function* _fetchAllListUltility(action) {
  try {
    let currentService = yield select(makeSelectUtilityFreeServiceContainer());
    let res = yield window.connection.fetchAllUtilitiIServiceItems({
      ...action.payload,
      pageSize: 2000,
      service_map_management_id: currentService.data.id,
    });
    if (res.success) {
      yield put(
        fetchAllListUltilityComplete({
          data: res.data.items,
        })
      );
    } else {
      yield put(fetchAllListUltilityComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(fetchAllListUltilityComplete());
  }
}
function* _deleteListUltility(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.deleteUtilitiIServiceItems(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete amenity successful.");
      } else {
        notificationBar("Xóa tiện ích thành công.");
      }
      callback && callback();
    } else {
      yield put(deleteListUltilityComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(deleteListUltilityComplete());
  }
}
function* _importListUltility(action) {
  try {
    let res = yield window.connection.importListUltility({
      file_path: action.payload,
    });
    if (res.success) {
      yield put(importListUltilityComplete(true));
    } else {
      yield put(importListUltilityComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(importListUltilityComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_APARTMENT, _fetchApartment),
    takeLatest(CREATE_PAYMENT, _createListUltility),
    takeLatest(FETCH_ALL_ITEMS, _fetchAllListUltility),
    takeLatest(DELETE_PAYMENT, _deleteListUltility),
    takeLatest(UPDATE_PAYMENT, _updateListUltility),
    takeLatest(IMPORT_PAYMENT, _importListUltility),
  ]);
}
