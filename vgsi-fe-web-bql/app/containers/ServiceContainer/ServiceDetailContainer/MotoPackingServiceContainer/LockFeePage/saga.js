import { all, put, select, takeLatest } from 'redux-saga/effects';
import { FETCH_APARTMENT, FETCH_ALL_PAYMENT } from './constants';
import { fetchApartmentComplete, fetchAllPaymentComplete } from './actions';
import makeSelectMotoPackingServiceContainer from '../selectors';

function* _fetchApartment(action) {
  try {
    let res = yield window.connection.fetchAllApartment({ page: 1, pageSize: 20, ...action.payload });
    if (res.success) {
      yield put(fetchApartmentComplete(res.data.items));
    } else {
      yield put(fetchApartmentComplete());
    }
  } catch (error) {
    yield put(fetchApartmentComplete())
  }
}

function* _fetchAllPayment(action) {
  try {
    let currentService = yield select(makeSelectMotoPackingServiceContainer())
    let res = yield window.connection.fetchMotoPackingFee({
      ...action.payload, pageSize: 20,
      service_map_management_id: currentService.data.id,
      status: 1
    });
    if (res.success) {
      yield put(fetchAllPaymentComplete({
        data: res.data.items,
        totalPage: res.data.pagination.totalCount
      }));
    } else {
      yield put(fetchAllPaymentComplete());
    }
  } catch (error) {
    console.log(error)
    yield put(fetchAllPaymentComplete())
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_APARTMENT, _fetchApartment),
    takeLatest(FETCH_ALL_PAYMENT, _fetchAllPayment),
  ])
}
