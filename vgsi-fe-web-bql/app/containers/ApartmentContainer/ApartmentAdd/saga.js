import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { notification } from "antd";
import {
  FETCH_BUILDING_AREA,
  CREATE_APARTMENT,
  FETCH_DETAIL_APARTMENT,
  FETCH_ALL_APARTMENT_TYPE,
  FETCH_ALL_RESIDENT_BY_PHONE,
} from "./constants";
import { parseTree, notificationBar, notificationBar2 } from "../../../utils";
import { selectBuildingCluster } from "../../../redux/selectors";
import {
  fetchBuildingAreaCompleteAction,
  createApartmentCompleteAction,
  fetchDetailApartmentCompleteAction,
  fetchAllApartmentTypeComplete,
  fetchAllResidentByPhoneCompleteAction,
} from "./actions";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

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
function* _createApartment(action) {
  try {
    let res = yield window.connection.createApartment(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Add property successful.");
      } else {
        notificationBar("Thêm mới bất động sản thành công.");
      }
      yield put(createApartmentCompleteAction(true));
    } else {
      // if (language === "en" && res.statusCode === 501) {
      //   notificationBar2("Property name already exist");
      // }
      yield put(createApartmentCompleteAction());
    }
  } catch (error) {
    yield put(createApartmentCompleteAction());
  }
}
function* _fetchDetailApartment(action) {
  try {
    let res = yield window.connection.fetchDetailApartment(action.payload);
    if (res.success) {
      yield put(fetchDetailApartmentCompleteAction(res.data));
    } else {
      yield put(fetchDetailApartmentCompleteAction());
    }
  } catch (error) {
    yield put(fetchDetailApartmentCompleteAction());
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
    takeLatest(FETCH_BUILDING_AREA, _fetchBuildingArea),
    takeLatest(CREATE_APARTMENT, _createApartment),
    takeLatest(FETCH_DETAIL_APARTMENT, _fetchDetailApartment),
    takeLatest(FETCH_ALL_APARTMENT_TYPE, _fetchApartmentType),
    takeLatest(FETCH_ALL_RESIDENT_BY_PHONE, _fetchResidentByPhone),
  ]);
}
