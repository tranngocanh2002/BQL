import { all, put, takeLatest } from 'redux-saga/effects';
import { FETCH_ALL_OLD_RESIDENT } from './constants';
import { fetchAllOldResidentCompleteAction } from './actions';
import config from '../../../utils/config'

function* _fetchOldResident(action) {
  try {
    let res = yield window.connection.fetchOldResident({ ...action.payload, pageSize: 20 })
    if (res.success) {
      yield put(fetchAllOldResidentCompleteAction({
        data: res.data.items.map(mm => ({ ...mm, type_name: (config.TYPE_RESIDENT.find(ii => ii.id == mm.type) || {}).name })),
        totalPage: res.data.pagination.totalCount
      }))
    } else {
      yield put(fetchAllOldResidentCompleteAction())
    }
  } catch (error) {
    yield put(fetchAllOldResidentCompleteAction())
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_OLD_RESIDENT, _fetchOldResident),
  ])
}
