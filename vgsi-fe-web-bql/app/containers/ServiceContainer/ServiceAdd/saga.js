import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_DETAIL_SERVICE_PROVIDER,
  UPDATE_SERVICE_PROVIDER,
  FETCH_DETAIL_SERVICE_CLOUD,
  FETCH_SERVICE_PROVIDER,
  ADD_SERVICE,
} from "./constants";
import {
  fetchDetailServiceCloudComplete,
  fetchServiceProviderComplete,
  addServiceComplete,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchDetailServiceCloud(action) {
  try {
    let res = yield window.connection.fetchDetailServiceCloud(action.payload);
    if (res.success) {
      yield put(fetchDetailServiceCloudComplete(res.data));
    } else {
      yield put(fetchDetailServiceCloudComplete());
    }
  } catch (error) {
    yield put(fetchDetailServiceCloudComplete());
  }
}
function* _fetchServiceProvider(action) {
  try {
    let res = yield window.connection.fetchServiceProvider({
      ...action.payload,
      pageSize: 2000,
    });
    if (res.success) {
      yield put(fetchServiceProviderComplete(res.data.items));
    } else {
      yield put(fetchServiceProviderComplete());
    }
  } catch (error) {
    yield put(fetchServiceProviderComplete());
  }
}
function* _addService(action) {
  try {
    let res = yield window.connection.addService(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Add service successful.");
      } else {
        notificationBar("Thêm dịch vụ thành công.");
      }
      yield put(addServiceComplete(true));
    } else {
      yield put(addServiceComplete());
    }
  } catch (error) {
    yield put(addServiceComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_DETAIL_SERVICE_CLOUD, _fetchDetailServiceCloud),
    takeLatest(FETCH_SERVICE_PROVIDER, _fetchServiceProvider),
    takeLatest(ADD_SERVICE, _addService),
  ]);
}
