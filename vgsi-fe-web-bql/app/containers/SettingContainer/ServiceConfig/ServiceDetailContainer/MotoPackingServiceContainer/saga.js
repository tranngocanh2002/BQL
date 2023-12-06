import { all, put, takeLatest } from 'redux-saga/effects';
import { FETCH_DETAIL_SERVICE } from './constants';
import { fetchDetailServiceComplete } from './actions';



function* _fetchDetailService(action) {
  try {
    let res = yield Promise.all([
      window.connection.fetchAllService({ page: 1, pageSize: 2000, service_base_url: action.payload }),
      window.connection.fetchDetailServiceVehicleConfig({ service_base_url: action.payload }),
    ])
    if (res[0].success && res[0].data.items.length == 1) {
      if (!!!res[1].data) {
        let resCreateDetail = yield window.connection.updateServiceVehicleConfig({
          auto_create_fee: 1,
          service_map_management_id: res[0].data.items[0].id
        })

        if (resCreateDetail.success) {
          yield put(fetchDetailServiceComplete({
            ...res[0].data.items[0],
            config: resCreateDetail.data
          }));
        } else {
          yield put(fetchDetailServiceComplete());
        }

      } else {
        yield put(fetchDetailServiceComplete({
          ...res[0].data.items[0],
          config: res[1].data
        }));
      }
    } else {
      yield put(fetchDetailServiceComplete());
    }
  } catch (error) {
    yield put(fetchDetailServiceComplete())
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_DETAIL_SERVICE, _fetchDetailService),
  ])
}
