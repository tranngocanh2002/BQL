import {
  take,
  all,
  put,
  takeEvery,
  takeLatest,
  call,
  select,
} from "redux-saga/effects";
import {
  FETCH_ALL_RESIDENT,
  DELETE_RESIDENT,
  UPDATE_DETAIL,
  FETCH_APARTMENT_OF_RESIDENT,
  IMPORT_RESIDENT,
} from "./constants";
import {
  fetchAllResidentCompleteAction,
  deleteResidentCompleteAction,
  updateDetailCompleteAction,
  fetchApartmentOfResidentCompleteAction,
  importResidentComplete,
} from "./actions";
import { delay } from "redux-saga";
import config from "../../../utils/config";
import { notification } from "antd";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchResident(action) {
  try {
    let res = yield window.connection.fetchResident({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchAllResidentCompleteAction({
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
      yield put(fetchAllResidentCompleteAction());
    }
  } catch (error) {
    yield put(fetchAllResidentCompleteAction());
  }
  // yield put(loginSuccess())
}

function* _deleteResident(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.removeMemberOfApartment(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete resident successful.");
      } else {
        notificationBar("Xóa cư dân thành công.");
      }
      callback && callback();
    } else {
      yield put(deleteResidentCompleteAction());
    }
  } catch (error) {
    yield put(deleteResidentCompleteAction());
  }
}

function* _updateDetail(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.updateResident(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update resident successful.");
      } else {
        notificationBar("Cập nhật cư dân thành công.");
      }
      callback && callback();
    } else {
      yield put(updateDetailCompleteAction());
    }
  } catch (error) {
    yield put(updateDetailCompleteAction());
  }
}

function* _fetchApartments(action) {
  try {
    let res = yield window.connection.fetchApartmentByResident(action.payload);
    if (res.success) {
      yield delay(500);
      yield put(
        fetchApartmentOfResidentCompleteAction({
          ...action.payload,
          lst: res.data.items,
        })
      );
    }
  } catch (error) {}
}

function* _importResident(action) {
  try {
    let res = yield window.connection.importResident({
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
      yield put(importResidentComplete(true));
    } else {
      yield put(importResidentComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(importResidentComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_RESIDENT, _fetchResident),
    takeLatest(DELETE_RESIDENT, _deleteResident),
    takeLatest(UPDATE_DETAIL, _updateDetail),
    takeEvery(FETCH_APARTMENT_OF_RESIDENT, _fetchApartments),
    takeEvery(IMPORT_RESIDENT, _importResident),
  ]);
}
