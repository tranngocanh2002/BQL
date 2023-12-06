import { take, call, put, select, all, takeLatest } from 'redux-saga/effects';

import { FETCH_ALL_BILL, FETCH_APARTMENT, FETCH_BUILDING_AREA } from "./constants";
import { fetchAllBillComplete, fetchApartmentCompleteAction, fetchBuildingAreaCompleteAction } from "./actions";


function* _fetchAllBill(action) {
	try {
		let res = yield window.connection.fetchAllBill({
			...action.payload, pageSize: 20,
			status: 10
		});
		if (res.success) {
			yield put(fetchAllBillComplete({
				data: res.data.items,
				totalPage: res.data.pagination.totalCount,
				total_count: res.data.total_count
			}));
		} else {
			yield put(fetchAllBillComplete());
		}
	} catch (error) {
		yield put(fetchAllBillComplete());
	}
}

function* _fetchAllApartment(action) {
	try {
		let res = yield window.connection.fetchAllApartment({ ...action.payload, pageSize: 20 })
		if (res.success) {
			yield put(fetchApartmentCompleteAction(res.data.items))
		} else {
			yield put(fetchApartmentCompleteAction())
		}
	} catch (error) {
		yield put(fetchApartmentCompleteAction())
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
export default function* CashBookSaga() {
	yield all([
		takeLatest(FETCH_ALL_BILL, _fetchAllBill),
		takeLatest(FETCH_APARTMENT, _fetchAllApartment),
		takeLatest(FETCH_BUILDING_AREA, _fetchBuildingArea)
	]);
}
