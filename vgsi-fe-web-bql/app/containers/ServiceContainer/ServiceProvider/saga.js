import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { parseTree, notificationBar } from "../../../utils";
import {
  FETCH_ALL_NOTIFICATIOIN_ACTION,
  FETCH_NOTIFICATION_CATEGORY_ACTION,
  FETCH_PROVIDERS,
  DELETE_SERVICE_PROVIDER,
} from "./constants";
import {
  fetchNotificationCompleteAction,
  fetchCategoryNotificationCompleteAction,
  fetchProvidersCompleteAction,
  deleteServiceProviderCompleteAction,
} from "./actions";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchProviders(action) {
  try {
    let res = yield window.connection.fetchServiceProvider({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchProvidersCompleteAction({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchProvidersCompleteAction());
    }
  } catch (error) {
    yield put(fetchProvidersCompleteAction());
  }
}
function* _deleteServiceProvider(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.deleteServiceProvider(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete contractor successful.");
      } else {
        notificationBar("Xóa nhà cung cấp thành công.");
      }
      callback && callback();
    } else {
      yield put(deleteServiceProviderCompleteAction());
    }
  } catch (error) {
    yield put(deleteServiceProviderCompleteAction());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_PROVIDERS, _fetchProviders),
    takeLatest(DELETE_SERVICE_PROVIDER, _deleteServiceProvider),
  ]);
}
