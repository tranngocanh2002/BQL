import {
  FETCH_ALL_COMBINE_CARD,
  DELETE_COMBINE_CARD,
  FETCH_BUILDING_AREA,
  UPDATE_DETAIL,
  IMPORT_COMBINE_CARD,
  CREATE_COMBINE_CARD,
} from "./constants";

import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  fetchAllCombineCardCompleteAction,
  deleteCombineCardCompleteAction,
  updateCombineCardAction,
  updateCombineCardCompleteAction,
  importCombineCardComplete,
  createCombineCardCompleteAction,
} from "./actions";
import { notification } from "antd";
import { saveToken } from "../../../redux/actions/config";
import { notificationBar, parseTree } from "../../../utils/index";
import { selectBuildingCluster } from "../../../redux/selectors";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchCombineCard(action) {
  try {
    let res = yield window.connection.fetchAllCombineCard({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchAllCombineCardCompleteAction({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchAllCombineCardCompleteAction());
    }
  } catch (error) {
    yield put(fetchAllCombineCardCompleteAction());
  }
  // yield put(loginSuccess())
}

function* _deleteCombineCard(action) {
  try {
    const { id, callback } = action.payload;
    let res = yield window.connection.deleteCombineCard({ id });
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete merge card successful.");
      } else {
        notificationBar("Xóa thẻ hợp nhất thành công.");
      }
      callback && callback();
    } else {
      yield put(deleteCombineCardCompleteAction());
    }
  } catch (error) {
    yield put(deleteCombineCardCompleteAction());
  }
}

function* _updateDetail(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.updateCombineCard(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update merge card successful.");
      } else {
        notificationBar("Cập nhật thẻ hợp nhất thành công.");
      }
      yield put(updateCombineCardCompleteAction());
      callback && callback();
    } else {
      yield put(updateCombineCardCompleteAction());
    }
  } catch (error) {
    yield put(updateCombineCardCompleteAction());
  }
}
function* _createCombineCard(action) {
  try {
    const { callback } = action.payload;
    let res = yield window.connection.createCombineCard(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Add card successful.");
      } else {
        notificationBar("Thêm mới thẻ thành công.");
      }
      yield put(createCombineCardCompleteAction(true));
      callback && callback();
    } else {
      // if (language === "en" && res.statusCode === 501) {
      //   notificationBar2("Property name already exist");
      // }
      yield put(createCombineCardCompleteAction());
    }
  } catch (error) {
    yield put(createCombineCardCompleteAction());
  }
}
function* _importCombineCard(action) {
  try {
    let res = yield window.connection.importCombineCard({
      ...action.payload,
      is_validate: 0,
    });
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Import data successful.");
      } else {
        notificationBar("Import dữ liệu thành công.");
      }
      yield put(importCombineCardComplete(true));
    } else {
      yield put(importCombineCardComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(importCombineCardComplete());
  }
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(FETCH_ALL_COMBINE_CARD, _fetchCombineCard),
    takeLatest(DELETE_COMBINE_CARD, _deleteCombineCard),
    takeLatest(UPDATE_DETAIL, _updateDetail),
    takeLatest(IMPORT_COMBINE_CARD, _importCombineCard),
    takeLatest(CREATE_COMBINE_CARD, _createCombineCard),
  ]);
}
