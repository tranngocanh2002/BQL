import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_COUNT_REPORT,
  FETCH_ANNOUNCEMENT,
  FETCH_REQUEST,
  FETCH_FINANCE,
  BOOKING_REVENUE,
  BOOKING_LIST_REVENUE,
  FETCH_APARTMENT,
  FETCH_RESIDENT,
  FETCH_MAINTENANCE,
} from "./constants";
import {
  fetchCountReportComplete,
  fetchAnnouncementComplete,
  fetchRequestComplete,
  fetchFinanceComplete,
  fetchBookingRevenueComplete,
  fetchBookingListRevenueComplete,
  fetchApartmentComplete,
  fetchResidentComplete,
  fetchMaintenanceComplete,
} from "./actions";

import makeSelectDashboard from "./selectors";
import moment from "moment";

function* _countAll(action) {
  try {
    let res = yield window.connection.dashboardCountAll(action.payload);
    if (res.success) {
      yield put(fetchCountReportComplete(res.data));
    } else {
      yield put(fetchCountReportComplete());
    }
  } catch (error) {
    yield put(fetchCountReportComplete());
  }
}

function* _fetchAnnouncement(action) {
  try {
    let res = yield window.connection.dashboardFetchAnnouncement(
      action.payload
    );
    if (res.success) {
      yield put(fetchAnnouncementComplete(res.data));
    } else {
      yield put(fetchAnnouncementComplete());
    }
  } catch (error) {
    yield put(fetchAnnouncementComplete());
  }
}
function* _fetchRequest(action) {
  try {
    let { month } = action.payload || {};
    let dashboard = yield select(makeSelectDashboard());
    if (!month) {
      month = dashboard.request.month;
    }
    let convert_month = moment.unix(month);
    let from_day = moment(convert_month).startOf("month").unix();
    let to_day = moment(convert_month).endOf("month").unix();
    let res = yield window.connection.dashboardFetchRequest({
      from_day,
      to_day,
    });
    if (res.success) {
      yield put(fetchRequestComplete(res.data));
    } else {
      yield put(fetchRequestComplete());
    }
  } catch (error) {
    console.log("error", error);
    yield put(fetchRequestComplete());
  }
}

function* _fetchMaintenance(action) {
  try {
    let { from_month, to_month } = action.payload || {};
    let dashboard = yield select(makeSelectDashboard());
    if (!from_month) {
      from_month = dashboard.maintenance.from_month;
    }
    if (!to_month) {
      to_month = dashboard.maintenance.to_month;
    }
    let res = yield window.connection.dashboardFetchMaintenance({
      from_month,
      to_month,
    });
    if (res.success) {
      yield put(fetchMaintenanceComplete(res.data));
    } else {
      yield put(fetchMaintenanceComplete());
    }
  } catch (error) {
    console.log("error", error);
    yield put(fetchMaintenanceComplete());
  }
}

function* _fetchFinance(action) {
  try {
    let { from_month, to_month } = action.payload || {};
    let dashboard = yield select(makeSelectDashboard());
    if (!from_month) {
      from_month = dashboard.finance.from_month;
    }
    if (!to_month) {
      to_month = dashboard.finance.to_month;
    }
    let res = yield window.connection.dashboardFetchFinance({
      from_month,
      to_month,
    });
    if (res.success) {
      yield put(fetchFinanceComplete(res.data));
    } else {
      yield put(fetchFinanceComplete());
    }
  } catch (error) {
    console.log("error", error);
    yield put(fetchFinanceComplete());
  }
}

function* _fetchBookingRevenue(action) {
  try {
    let { month } = action.payload || {};
    let dashboard = yield select(makeSelectDashboard());
    if (!month) {
      month = dashboard.booking.month;
    }
    let convert_month = moment.unix(month);
    let from_date = moment(convert_month).startOf("month").unix();
    let to_date = moment(convert_month).endOf("month").unix();
    let res = yield window.connection.dashboardFetchTotalRevenue({
      from_date,
      to_date,
      month: moment(convert_month).format("M"),
      year: moment(convert_month).format("YYYY"),
    });
    if (res.success) {
      yield put(fetchBookingRevenueComplete(res.data));
    } else {
      yield put(fetchBookingRevenueComplete());
    }
  } catch (error) {
    console.log("error", error);
    yield put(fetchBookingRevenueComplete());
  }
}

function* _fetchBookingListRevenue(action) {
  try {
    let { month } = action.payload || {};
    let dashboard = yield select(makeSelectDashboard());
    if (!month) {
      month = dashboard.booking_list.month;
    }
    let convert_month = moment.unix(month);
    let start_date = moment(convert_month).startOf("month").unix();
    let end_date = moment(convert_month).endOf("month").unix();
    let res = yield window.connection.dashboardBookingListRevenue({
      start_date,
      end_date,
    });
    if (res.success) {
      yield put(fetchBookingListRevenueComplete(res.data));
    } else {
      yield put(fetchBookingListRevenueComplete());
    }
  } catch (error) {
    console.log("error", error);
    yield put(fetchBookingRevenueComplete());
  }
}

function* _fetchApartment(action) {
  try {
    let res = yield window.connection.dashboardFetchApartment();
    if (res.success) {
      yield put(fetchApartmentComplete(res.data));
    } else {
      yield put(fetchApartmentComplete());
    }
  } catch (error) {
    console.log("error", error);
    yield put(fetchApartmentComplete());
  }
}

function* _fetchResident(action) {
  try {
    let res = yield window.connection.dashboardFetchResident();
    if (res.success) {
      yield put(fetchResidentComplete(res.data));
    } else {
      yield put(fetchResidentComplete());
    }
  } catch (error) {
    console.log("error", error);
    yield put(fetchResidentComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_COUNT_REPORT, _countAll),
    takeLatest(FETCH_ANNOUNCEMENT, _fetchAnnouncement),
    takeLatest(FETCH_REQUEST, _fetchRequest),
    takeLatest(FETCH_MAINTENANCE, _fetchMaintenance),
    takeLatest(FETCH_FINANCE, _fetchFinance),
    takeLatest(BOOKING_REVENUE, _fetchBookingRevenue),
    takeLatest(BOOKING_LIST_REVENUE, _fetchBookingListRevenue),
    takeLatest(FETCH_APARTMENT, _fetchApartment),
    takeLatest(FETCH_RESIDENT, _fetchResident),
  ]);
}
