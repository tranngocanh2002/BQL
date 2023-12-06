import {
  take,
  all,
  put,
  select,
  takeLatest,
  takeEvery,
} from "redux-saga/effects";
import { notificationBar } from "../../../../../utils";
import {
  FETCH_ALL_CONFIG,
  CREATE_CONFIG,
  FETCH_CONFIG_PRICE,
  CREATE_CONFIG_PRICE,
  DELETE_CONFIG_PRICE,
  FETCH_APARTMENT,
  CREATE_BOOKING,
  FETCH_BOOKING,
  FETCH_SLOT_FREE,
} from "./constants";
import {
  fetchAllConfigComplete,
  fetchApartmentCompleteAction,
  createBookingComplete,
  fetchBookingComplete,
  fetchSlotFreeComplete,
} from "./actions";
import moment from "moment";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchAllConfig(action) {
  try {
    let res = yield window.connection.fetchAllConfigUtilityServiceItem({
      pageSize: 1000,
      service_utility_free_id: action.payload,
    });
    if (res.success) {
      yield put(fetchAllConfigComplete(res.data.items));
    } else {
      yield put(fetchAllConfigComplete());
    }
  } catch (error) {
    yield put(fetchAllConfigComplete());
  }
}

function* _fetchApartment(action) {
  try {
    let res = yield window.connection.fetchAllApartment({
      ...action.payload,
      pageSize: 200000,
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

function* _createBooking(action) {
  try {
    let res = yield window.connection.createBookingUtility({
      ...action.payload,
    });
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Create booking successful.");
      } else {
        notificationBar("Tạo đặt chỗ thành công.");
      }
      yield put(createBookingComplete(true));
    } else {
      yield put(createBookingComplete());
    }
  } catch (error) {
    yield put(createBookingComplete());
  }
  // yield put(loginSuccess())
}

function* _fetchBooking(action) {
  try {
    let { dates, ...rest } = action.payload;
    let res = yield Promise.all([
      window.connection.fetchBookingUtility({ ...rest, pageSize: 200000 }),
      window.connection.fetchReportByDateBookingUtility({
        start_date: rest.start_time_from,
        end_date: rest.start_time_to,
        service_utility_config_id: rest.service_utility_config_id,
      }),
    ]);

    let items = {};

    if (res[0].success) {
      res[0].data.items.forEach((ii) => {
        let start_time = moment.unix(ii.start_time).startOf("day").unix();
        if (!items[start_time]) {
          items[start_time] = {
            books: [ii],
            statics: [],
          };
        } else {
          items[start_time].books.push(ii);
        }
      });
    }
    if (res[1].success) {
      res[1].data.forEach((ii) => {
        let start_time = moment.unix(ii.date).startOf("day").unix();
        if (!items[start_time]) {
          items[start_time] = {
            books: [],
            statics: ii.time,
          };
        } else {
          items[start_time].statics = ii.time;
        }
      });
    }

    yield put(fetchBookingComplete({ ...action.payload, items }));
  } catch (error) {
    yield put(fetchBookingComplete({ ...action.payload, items: {} }));
  }
  // yield put(loginSuccess())
}

function* _fetchSlotFree(action) {
  try {
    let res = yield window.connection.fetchConfigPrice({
      ...action.payload,
      pageSize: 1000,
    });
    if (res.success) {
      yield put(
        fetchSlotFreeComplete({ ...action.payload, items: res.data.items })
      );
    }
  } catch (error) {}
  // yield put(loginSuccess())
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_CONFIG, _fetchAllConfig),
    takeLatest(FETCH_APARTMENT, _fetchApartment),
    takeLatest(CREATE_BOOKING, _createBooking),
    takeLatest(FETCH_BOOKING, _fetchBooking),
    takeLatest(FETCH_SLOT_FREE, _fetchSlotFree),
  ]);
}
