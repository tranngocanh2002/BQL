import { take, call, put, select, all, takeLatest } from "redux-saga/effects";

import {
  FETCH_ALL_BILL,
  FETCH_APARTMENT,
  FETCH_BUILDING_AREA,
  BLOCK_BILL,
} from "./constants";
import {
  fetchAllBillComplete,
  fetchApartmentCompleteAction,
  fetchBuildingAreaCompleteAction,
  blockBillComplete,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchAllBill(action) {
  try {
    let res = yield window.connection.fetchAllBill({
      ...action.payload,
      type: 0,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchAllBillComplete({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchAllBillComplete());
    }
  } catch (error) {
    yield put(fetchAllBillComplete());
  }
}

function* _fetchAllApartment(action) {
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
function* _fetchBuildingArea(action) {
  try {
    let res = yield window.connection.getBuildingArea({ pageSize: 20 });
    if (res.success) {
      yield put(
        fetchBuildingAreaCompleteAction(
          res.data.items.filter((area) => !!area.parent_id)
        )
      );
    } else {
      yield put(fetchBuildingAreaCompleteAction([]));
    }
  } catch (error) {
    yield put(fetchBuildingAreaCompleteAction([]));
  }
  // yield put(loginSuccess())
}
function* _blockBill(action) {
  try {
    let res = yield window.connection.blockBill(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Closing entry successful.");
      } else {
        notificationBar("Chốt sổ thành công.");
      }
      yield put(blockBillComplete());
    } else {
      yield put(blockBillComplete());
    }
  } catch (error) {
    yield put(blockBillComplete());
  }
  // yield put(loginSuccess())
}

// Individual exports for testing
export default function* billListSaga() {
  yield all([
    takeLatest(FETCH_ALL_BILL, _fetchAllBill),
    takeLatest(FETCH_APARTMENT, _fetchAllApartment),
    takeLatest(FETCH_BUILDING_AREA, _fetchBuildingArea),
    takeLatest(BLOCK_BILL, _blockBill),
  ]);
}
