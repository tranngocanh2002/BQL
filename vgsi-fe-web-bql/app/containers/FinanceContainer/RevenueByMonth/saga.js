import { all, put, takeLatest } from "redux-saga/effects";
import { FETCH_REVENUE_BY_MONTH_ACTION } from "./constants";
import { fetchRevenueByMonthCompleteAction } from "./actions";

function* _fetchRevenueByMonth(action) {
  try {
    let { from_month, to_month, type } = action.payload || {};
    let res = yield window.connection.dashboardFetchFinance({
      from_month,
      to_month,
      type,
    });
    if (res.success) {
      yield put(
        fetchRevenueByMonthCompleteAction({
          data: res.data,
        })
      );
    } else {
      yield put(fetchRevenueByMonthCompleteAction());
    }
  } catch (error) {
    console.log("error", error);
    yield put(fetchRevenueByMonthCompleteAction());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([takeLatest(FETCH_REVENUE_BY_MONTH_ACTION, _fetchRevenueByMonth)]);
}
