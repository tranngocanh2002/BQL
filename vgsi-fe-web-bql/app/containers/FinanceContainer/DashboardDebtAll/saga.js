import { take, call, put, select, all, takeLatest } from "redux-saga/effects";
import {
  FETCH_ALL_FEE,
  FETCH_APARTMENT,
  FETCH_SERVICE_MAP,
  DELETE_FEE,
  UPDATE_PAYMENT,
  FETCH_BUILDING_AREA,
  FETCH_DEBT,
} from "./constants";
import {
  fetchAllFeeComplete,
  fetchApartmentCompleteAction,
  fetchServiceMapCompleteAction,
  deleteFeeCompleteAction,
  updatePaymentComplete,
  fetchBuildingAreaCompleteAction,
  fetchDebtComplete,
} from "./actions";
import { notificationBar } from "../../../utils";

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
}

function* _fetchDebt(action) {
  try {
    let res = yield window.connection.fetchAllDebt({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchDebtComplete({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
          total_count: res.data.total_count,
        })
      );
    } else {
      yield put(fetchDebtComplete());
    }
  } catch (error) {
    yield put(fetchDebtComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_BUILDING_AREA, _fetchBuildingArea),
    takeLatest(FETCH_APARTMENT, _fetchAllApartment),
    takeLatest(FETCH_DEBT, _fetchDebt),
  ]);
}
