import { take, all, put, takeEvery, takeLatest, call } from 'redux-saga/effects';
import { FETCH_ALL_LUCID, FETCH_ALL_RESIDENT, FETCH_VEHICLE, FETCH_ALL_APARTMENT } from './constants';
import { fetchAllLucidComplete, fetchAllResidentComplete, fetchVehicleComplete, fetchApartmentComplete } from './actions';
import { delay } from 'redux-saga'
import config from '../../../utils/config'
import { notification } from "antd";
import { notificationBar } from "../../../utils";

function* _fetchAllLucid(action) {
  try {
    let res = yield window.connection.lucidGetAll({ ...action.payload, pageSize: 20 })
    if (res.success) {
      yield put(fetchAllLucidComplete({
        data: res.data.items,
        totalPage: res.data.pagination.totalCount
      }))
    } else {
      yield put(fetchAllLucidComplete())
    }
  } catch (error) {
    yield put(fetchAllLucidComplete())
  }
}

function* _fetchALlResident(action) {
  try {
    let res = yield window.connection.fetchResident({ ...action.payload, pageSize: 20000 })
    if (res.success) {
      yield put(fetchAllResidentComplete(res.data.items))
    } else {
      yield put(fetchAllResidentComplete())
    }
  } catch (error) {
    yield put(fetchAllResidentComplete())
  }
}

function* _fetchVehicle(action) {
  try {
    let res = yield window.connection.fetchAllVehicle({ ...action.payload, pageSize: 20000 })
    if (res.success) {
      yield put(fetchVehicleComplete(res.data.items))
    } else {
      yield put(fetchVehicleComplete())
    }
  } catch (error) {
    yield put(fetchVehicleComplete())
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

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_LUCID, _fetchAllLucid),
    takeLatest(FETCH_ALL_RESIDENT, _fetchALlResident),
    takeLatest(FETCH_VEHICLE, _fetchVehicle),
    takeLatest(FETCH_ALL_APARTMENT, _fetchAllApartment),
  ])
}
