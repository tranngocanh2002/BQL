import {
  LOGIN,
  FETCH_ALL_STAFF,
  DELETE_STAFF,
  FETCH_ALL_APARTMENT,
  DELETE_APARTMENT,
  FTECH_APARTMENT,
  CREATE_RESIDENT,
  FETCH_ALL_RESIDENT_BY_PHONE,
} from "./constants";

import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  fetchApartmentCompleteAction,
  createResidentCompleteAction,
  fetchAllResidentByPhoneCompleteAction,
} from "./actions";
import { notification } from "antd";
import { saveToken } from "../../../redux/actions/config";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchApartment(action) {
  try {
    let res = yield window.connection.fetchAllApartment({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(fetchApartmentCompleteAction(res.data.items));
    } else {
      yield put(fetchApartmentCompleteAction());
    }
  } catch (error) {
    yield put(fetchApartmentCompleteAction());
  }
  // yield put(loginSuccess())
}

function* _createResident(action) {
  try {
    let res = yield window.connection.createResident(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Add new resident successful.");
      } else {
        notificationBar("Thêm mới cư dân thành công.");
      }
      yield put(createResidentCompleteAction(true));
    } else {
      yield put(createResidentCompleteAction());
    }
  } catch (error) {
    yield put(createResidentCompleteAction());
  }
  // yield put(loginSuccess())
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
    takeLatest(FTECH_APARTMENT, _fetchApartment),
    takeLatest(CREATE_RESIDENT, _createResident),
    takeLatest(FETCH_ALL_RESIDENT_BY_PHONE, _fetchResidentByPhone),
  ]);
}
