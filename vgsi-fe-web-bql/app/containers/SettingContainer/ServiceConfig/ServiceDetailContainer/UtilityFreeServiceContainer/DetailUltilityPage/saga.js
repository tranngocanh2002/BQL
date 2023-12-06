import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { all, put, select, takeLatest } from "redux-saga/effects";
import { notificationBar } from "../../../../../../utils";
import {
  addUltilityItemComlete,
  fetchDetailUltilityItemComlete,
  updateUltilityItemComlete,
} from "./actions";
import {
  ADD_ULTILITY_ITEM,
  FETCH_DETAIL,
  UPDATE_ULTILITY_ITEM,
} from "./constants";

function* _addItems(action) {
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.createUtilitiIServiceItems(
      action.payload
    );
    if (res.success) {
      if (language === "en") {
        notificationBar("Add amenity successful.");
      } else {
        notificationBar("Thêm tiện ích thành công.");
      }
      yield put(addUltilityItemComlete(true));
    } else {
      yield put(addUltilityItemComlete());
    }
  } catch (error) {
    yield put(addUltilityItemComlete());
  }
}

function* _updateItems(action) {
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.updateUtilitiIServiceItems(
      action.payload
    );
    if (res.success) {
      if (language === "en") {
        notificationBar("Update amenity successful.");
      } else {
        notificationBar("Cập nhật tiện ích thành công.");
      }
      yield put(updateUltilityItemComlete(true));
    } else {
      yield put(updateUltilityItemComlete());
    }
  } catch (error) {
    yield put(updateUltilityItemComlete());
  }
}

function* _fetchDetail(action) {
  try {
    let res = yield window.connection.getDetailUtilitiIServiceItems(
      action.payload
    );
    if (res.success) {
      yield put(fetchDetailUltilityItemComlete(res.data));
    } else {
      yield put(fetchDetailUltilityItemComlete());
    }
  } catch (error) {}
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(ADD_ULTILITY_ITEM, _addItems),
    takeLatest(FETCH_DETAIL, _fetchDetail),
    takeLatest(UPDATE_ULTILITY_ITEM, _updateItems),
  ]);
}
