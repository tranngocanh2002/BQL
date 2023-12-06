import { take, all, put, select, takeLatest, call } from 'redux-saga/effects';
import { FETCH_ALL_BILLS, FETCH_APARTMENT, FETCH_BUILDING_AREA } from './constants';
import { fetchAllBillsComplete, fetchApartmentComplete, fetchBuildingAreaCompleteAction } from './actions';


function* _fetchAllBills(action) {
  try {
    let res = yield window.connection.fetchBillsForReception({
      ...action.payload, pageSize: 20,
      status: 1, 
      type: 0
    });
    if (res.success) {
      yield put(fetchAllBillsComplete({
        data: res.data.items,
        total_count: res.data.total_count,
        totalPage: res.data.pagination.totalCount
      }));
    } else {
      yield put(fetchAllBillsComplete());
    }
  } catch (error) {
    yield put(fetchAllBillsComplete())
  }
}


function* _fetchAllApartment(action) {
  try {
    let res = yield window.connection.fetchAllApartment({ ...action.payload, pageSize: 20 })
    if (res.success) {
      yield put(fetchApartmentComplete(res.data.items))
    } else {
      yield put(fetchApartmentComplete())
    }
  } catch (error) {
    yield put(fetchApartmentComplete())
  }
  // yield put(loginSuccess())
}
function* _fetchBuildingArea(action) {
  try {
    let res = yield window.connection.getBuildingArea({ pageSize: 20 })
    if (res.success) {
      yield put(fetchBuildingAreaCompleteAction(res.data.items.filter(area => !!area.parent_id)))
    } else {
      yield put(fetchBuildingAreaCompleteAction([]))
    }
  } catch (error) {
    yield put(fetchBuildingAreaCompleteAction([]))
  }
  // yield put(loginSuccess())
}


// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_BILLS, _fetchAllBills),
    takeLatest(FETCH_APARTMENT, _fetchAllApartment),
    takeLatest(FETCH_BUILDING_AREA, _fetchBuildingArea)
  ])
}
