import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { FETCH_SERVICE_PROVIDER, UPDATE_SERVICE_DETAIL } from "./constants";
import {
  fetchServiceProviderComplete,
  updateServiceDetailComplete,
} from "./actions";
import { notificationBar } from "../../../../../../utils";
import { fetchAllServiceList } from "../../../ServiceList/actions";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

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
function* _updateServiceDetail(action) {
  const language = yield select(makeSelectLocale());
  try {
    const { config, ...rest } = action.payload;
    let res = yield Promise.all([
      window.connection.updateDetailService(rest),
      window.connection.updateServiceBuildingConfig(config),
    ]);
    if (res[0].success && res[1].success) {
      if (language === "en") {
        notificationBar("Update service information successful.");
      } else {
        notificationBar("Cập nhật thông tin dịch vụ thành công.");
      }
      yield put(
        updateServiceDetailComplete({
          ...res[0].data,
          config: res[1].data,
        })
      );
      yield put(fetchAllServiceList());
    } else {
      yield put(updateServiceDetailComplete());
    }
  } catch (error) {
    yield put(updateServiceDetailComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_SERVICE_PROVIDER, _fetchServiceProvider),
    takeLatest(UPDATE_SERVICE_DETAIL, _updateServiceDetail),
  ]);
}
