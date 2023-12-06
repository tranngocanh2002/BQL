import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { FETCH_ALL_TICKET, FETCH_APARTMENT, FETCH_CATEGORY } from "./constants";
import {
  fetchAllTicketCompleteAction,
  fetchCategoryCompleteAction,
  fetchApartmentCompleteAction,
} from "./actions";

function* _fetchAllTicket(action) {
  try {
    let res = yield window.connection.fetchAllTicket({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchAllTicketCompleteAction({
          data: res.data.items,
          totalPage: res.data.pagination ? res.data.pagination.totalCount : 1,
        })
      );
    } else {
      yield put(fetchAllTicketCompleteAction());
    }
  } catch (error) {
    yield put(fetchAllTicketCompleteAction());
  }
  // yield put(loginSuccess())
}

function* _fetchCategory(action) {
  try {
    let res = yield window.connection.fetchCategoryTicket({
      ...action.payload,
      pageSize: 2000,
    });
    if (res.success) {
      yield put(fetchCategoryCompleteAction(res.data.items));
    } else {
      yield put(fetchCategoryCompleteAction());
    }
  } catch (error) {
    yield put(fetchCategoryCompleteAction());
  }
  // yield put(loginSuccess())
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
  // yield put(loginSuccess())
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_TICKET, _fetchAllTicket),
    takeLatest(FETCH_APARTMENT, _fetchAllApartment),
    takeLatest(FETCH_CATEGORY, _fetchCategory),
  ]);
}
