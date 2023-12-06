import {
  LOGIN,
  FETCH_ALL_STAFF,
  DELETE_STAFF,
  FETCH_ALL_APARTMENT,
  DELETE_APARTMENT,
  FETCH_BUILDING_AREA,
  UPDATE_DETAIL,
  IMPORT_APARTMENT,
  FETCH_ALL_APARTMENT_TYPE,
  FETCH_ALL_RESIDENT_BY_PHONE,
} from "./constants";

import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  loginSuccess,
  loginFailed,
  deleteStaffCompleteAction,
  fetchAllApartmentCompleteAction,
  deleteApartmentCompleteAction,
  fetchBuildingAreaCompleteAction,
  updateApartmentAction,
  updateApartmentCompleteAction,
  importApartmentComplete,
  fetchAllApartmentTypeComplete,
  fetchAllResidentByPhoneCompleteAction,
} from "./actions";
import { notification } from "antd";
import { saveToken } from "../../../redux/actions/config";
import { notificationBar, parseTree } from "../../../utils/index";
import { selectBuildingCluster } from "../../../redux/selectors";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchApartment(action) {
  try {
    let res = yield window.connection.fetchAllApartment({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchAllApartmentCompleteAction({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchAllApartmentCompleteAction());
    }
  } catch (error) {
    yield put(fetchAllApartmentCompleteAction());
  }
  // yield put(loginSuccess())
}

function* _deleteApartment(action) {
  try {
    const { id, callback } = action.payload;
    let res = yield window.connection.deleteApartment({ id });
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete property successful.");
      } else {
        notificationBar("Xóa bất động sản thành công.");
      }
      callback && callback();
    } else {
      yield put(deleteApartmentCompleteAction());
    }
  } catch (error) {
    yield put(deleteApartmentCompleteAction());
  }
}

function* _fetchBuildingArea(action) {
  try {
    let res = yield window.connection.getBuildingArea({ pageSize: 10000 });
    if (res.success) {
      let root = yield select(selectBuildingCluster());
      yield put(
        fetchBuildingAreaCompleteAction(
          parseTree(
            root.data,
            res.data.items.map((iii) => ({
              children: [],
              key: `${iii.id}`,
              ...iii,
            }))
          )
        )
      );
    } else {
      yield put(fetchBuildingAreaCompleteAction([]));
    }
  } catch (error) {
    yield put(fetchBuildingAreaCompleteAction([]));
  }
}

function* _updateDetail(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.updateApartment(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update property successful.");
      } else {
        notificationBar("Cập nhật bất động sản thành công.");
      }
      yield put(updateApartmentCompleteAction());
      callback && callback();
    } else {
      yield put(updateApartmentCompleteAction());
    }
  } catch (error) {
    yield put(updateApartmentCompleteAction());
  }
}

function* _importApartment(action) {
  try {
    let res = yield window.connection.importApartment({
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
      yield put(importApartmentComplete(true));
    } else {
      yield put(importApartmentComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(importApartmentComplete());
  }
}

function* _fetchApartmentType(action) {
  try {
    let res = yield window.connection.fetchAllApartmentType({});
    if (res.success) {
      yield put(fetchAllApartmentTypeComplete(res.data));
    } else {
      yield put(fetchAllApartmentTypeComplete());
    }
  } catch (error) {
    yield put(fetchAllApartmentTypeComplete());
  }
}

function* _fetchResidentByPhone(action) {
  try {
    let res = yield window.connection.fetchResidentByPhone({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchAllResidentByPhoneCompleteAction({
          // data: res.data.items.map(mm => ({ id: mm.apartment_map_resident_user_id, phone: mm.phone, name: mm.first_name })),
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchAllResidentByPhoneCompleteAction());
    }
  } catch (error) {
    yield put(fetchAllResidentByPhoneCompleteAction());
  }
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(FETCH_ALL_APARTMENT, _fetchApartment),
    takeLatest(DELETE_APARTMENT, _deleteApartment),
    takeLatest(FETCH_BUILDING_AREA, _fetchBuildingArea),
    takeLatest(UPDATE_DETAIL, _updateDetail),
    takeLatest(IMPORT_APARTMENT, _importApartment),
    takeLatest(FETCH_ALL_APARTMENT_TYPE, _fetchApartmentType),
    takeLatest(FETCH_ALL_RESIDENT_BY_PHONE, _fetchResidentByPhone),
  ]);
}
