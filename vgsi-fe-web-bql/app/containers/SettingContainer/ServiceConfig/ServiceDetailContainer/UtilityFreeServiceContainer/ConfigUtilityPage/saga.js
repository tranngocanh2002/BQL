import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { notificationBar, notificationBar2 } from "../../../../../../utils";
import {
  FETCH_ALL_CONFIG,
  CREATE_CONFIG,
  FETCH_CONFIG_PRICE,
  CREATE_CONFIG_PRICE,
  DELETE_CONFIG_PRICE,
  DELETE_CONFIG_PLACE,
  UPDATE_CONFIG,
} from "./constants";
import {
  fetchAllConfigComplete,
  fetchAllConfig,
  createConfigComplete,
  fetchConfigPriceComplete,
  fetchConfigPrice,
  createConfigPriceComplete,
  deleteConfigPriceComplete,
  deleteConfigPlaceComplete,
  updateConfigComplete,
} from "./actions";
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

function* _createConfig(action) {
  const language = yield select(makeSelectLocale());

  try {
    let res = yield window.connection.createConfigUtilityServiceItem(
      action.payload
    );
    if (res.success) {
      yield put(fetchAllConfig(action.payload.service_utility_free_id));
      yield put(createConfigComplete(true));
      notificationBar("Tạo chỗ mới thành công.");
    } else {
      // if (language === "en" && res.statusCode === 501) {
      //   notificationBar2(
      //     "Place name or Place name (EN) already exists in the system"
      //   );
      // }
      yield put(createConfigComplete(false));
    }
  } catch (error) {
    yield put(createConfigComplete(false));
  }
}

function* _updateConfig(action) {
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.updateConfigUtilityServiceItem(
      action.payload
    );
    if (res.success) {
      yield put(fetchAllConfig(action.payload.service_utility_free_id));
      yield put(updateConfigComplete(true));
      if (language === "en") {
        notificationBar("Update slot successful.");
      } else {
        notificationBar("Cập nhật chỗ mới thành công.");
      }
    } else {
      yield put(updateConfigComplete(false));
    }
  } catch (error) {
    yield put(updateConfigComplete(false));
  }
}
function* _createConfigPrice(action) {
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.createConfigPrice(action.payload);

    if (res.success) {
      yield put(fetchConfigPrice(action.payload.service_utility_config_id));
      yield put(createConfigPriceComplete(true));
      if (language === "en") {
        notificationBar("Create successful.");
      } else {
        notificationBar("Tạo mới thành công.");
      }
    } else {
      yield put(createConfigPriceComplete(false));
    }
  } catch (error) {
    yield put(createConfigPriceComplete(false));
  }
}
function* _deleteConfigPrice(action) {
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.deleteConfigPrice(action.payload);
    if (res.success) {
      yield put(fetchConfigPrice(action.payload.service_utility_config_id));
      if (language === "en") {
        notificationBar("Delete successful.");
      } else {
        notificationBar("Xóa thành công.");
      }
    } else {
      yield put(deleteConfigPriceComplete(action.payload));
    }
  } catch (error) {
    yield put(deleteConfigPriceComplete(action.payload));
  }
}

function* _deleteConfigPlace(action) {
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.deleteConfigPlace(action.payload);
    if (res.success) {
      yield put(fetchAllConfig(action.payload.configId));
      if (language === "en") {
        notificationBar("Delete successful.");
      } else {
        notificationBar("Xóa thành công.");
      }
    } else {
      yield put(deleteConfigPlaceComplete(action.payload));
    }
  } catch (error) {
    yield put(deleteConfigPlaceComplete(action.payload));
  }
}

function* _fetchConfigPrice(action) {
  try {
    let res = yield window.connection.fetchConfigPrice({
      service_utility_config_id: action.payload,
      pageSize: 1000,
    });
    if (res.success) {
      yield put(
        fetchConfigPriceComplete({
          service_utility_config_id: action.payload,
          items: res.data.items,
        })
      );
    } else {
      yield put(
        fetchConfigPriceComplete({
          service_utility_config_id: action.payload,
          items: [],
        })
      );
    }
  } catch (error) {
    yield put(
      fetchConfigPriceComplete({
        service_utility_config_id: action.payload,
        items: [],
      })
    );
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_CONFIG, _fetchAllConfig),
    takeLatest(CREATE_CONFIG, _createConfig),
    takeLatest(UPDATE_CONFIG, _updateConfig),
    takeLatest(FETCH_CONFIG_PRICE, _fetchConfigPrice),
    takeLatest(CREATE_CONFIG_PRICE, _createConfigPrice),
    takeLatest(DELETE_CONFIG_PRICE, _deleteConfigPrice),
    takeLatest(DELETE_CONFIG_PLACE, _deleteConfigPlace),
  ]);
}
