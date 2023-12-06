import { take, call, put, select, all, takeLatest } from 'redux-saga/effects';

import { FETCH_ALL_BUILDING_AREA, FETCH_DETAIL_DEBT_AREA } from "./constants";
import { fetchAllBuildingAreaComplete, fetchDetailDebtArea, fetchDetailDebtAreaComplete } from './actions';
import moment from 'moment';


function* _fetchAllBuildingArea(action) {
  try {
    let res = yield window.connection.getBuildingArea({ pageSize: 200000 });
    if (res.success) {
      const area = res.data.items.filter(rr => !!!rr.parent_id).map(toa => {
        return {
          ...toa,
          floors: res.data.items.filter(rrrrr => rrrrr.parent_id == toa.id)
        }
      })
      yield put(fetchAllBuildingAreaComplete(area));
      if (area.length > 0) {
        yield put(fetchDetailDebtArea({ building_area: area[0], month: moment().startOf('month').unix() }))
      }
    } else {
      yield put(fetchAllBuildingAreaComplete([]));
    }
  } catch (error) {
    yield put(fetchAllBuildingAreaComplete([]));
  }
}
function* _fetchDetailDebtArea(action) {
  try {
    const { building_area, ...rest } = action.payload
    let res = yield window.connection.fetchAllDebt({
      ...rest,
      building_area_id: building_area.id,
      pageSize: 200000
    });
    if (res.success) {
      yield put(fetchDetailDebtAreaComplete({
        data: res.data.items,
        totalPage: res.data.pagination.totalCount,
        total_count: res.data.total_count,
      }));
    } else {
      yield put(fetchDetailDebtAreaComplete());
    }
  } catch (error) {
    yield put(fetchDetailDebtAreaComplete());
  }

}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_BUILDING_AREA, _fetchAllBuildingArea),
    takeLatest(FETCH_DETAIL_DEBT_AREA, _fetchDetailDebtArea),
  ]);
}
