import { all, put, takeLatest } from "redux-saga/effects";
import { CREATE_ACTIVE_CARD, FETCH_APARTMENT, FETCH_MEMBER } from "./constants";
import {
  createActiveCardComplete,
  fetchApartmentCompleteAction,
  fetchMemberCompleteAction,
} from "./actions";
import { notificationBar } from "utils";

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
function* _fetchMembers(action) {
  try {
    let res = yield window.connection.fetchMemberOfApartment(action.payload);
    if (res.success) {
      yield put(fetchMemberCompleteAction(res.data.items));
    } else {
      yield put(fetchMemberCompleteAction());
    }
  } catch (error) {
    yield put(fetchMemberCompleteAction());
  }
}
function* _createActiveCard(action) {
  try {
    let res = yield window.connection.createActiveCard(action.payload);
    if (res.success) {
      notificationBar("Kích hoạt thẻ thành công.");
      yield put(createActiveCardComplete({ status: true, data: res.data }));
    } else {
      yield put(createActiveCardComplete());
    }
  } catch (error) {
    yield put(createActiveCardComplete());
  }
}
// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_APARTMENT, _fetchAllApartment),
    takeLatest(FETCH_MEMBER, _fetchMembers),
    takeLatest(CREATE_ACTIVE_CARD, _createActiveCard),
  ]);
}
