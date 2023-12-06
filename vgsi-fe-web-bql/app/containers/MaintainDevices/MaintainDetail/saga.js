import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { FETCH_DETAIL_EQUIPMENT } from "./constants";
import { fetchEquipmentDetailCompleteAction } from "./actions";

function* _fetchEquimentDetail(action) {
  try {
    let res = yield window.connection.fetchDetailEquipment(action.payload);
    if (res.success) {
      yield put(fetchEquipmentDetailCompleteAction(res.data));
    } else {
      yield put(fetchEquipmentDetailCompleteAction());
    }
  } catch (error) {
    yield put(fetchEquipmentDetailCompleteAction());
  }
}
// Individual exports for testing
export default function* loginSaga() {
  yield all([takeLatest(FETCH_DETAIL_EQUIPMENT, _fetchEquimentDetail)]);
}
