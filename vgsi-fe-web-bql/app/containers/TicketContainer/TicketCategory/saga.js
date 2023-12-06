import {
  FETCH_AUTH_GROUP,
  CREATE_CATEGORY,
  FETCH_CATEGORY,
  DELETE_CATEGORY,
  UPDATE_CATEGORY,
} from "./constants";

import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  fetchAuthGroupCompleteAction,
  createCategoryCompleteAction,
  fetchCategoryCompleteAction,
  deleteCategoryCompleteAction,
  updateCategoryCompleteAction,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchAuthGroup(action) {
  try {
    let res = yield window.connection.getGroupAuth();
    if (res.success) {
      yield put(fetchAuthGroupCompleteAction(res.data));
    } else {
      yield put(fetchAuthGroupCompleteAction());
    }
  } catch (error) {
    yield put(fetchAuthGroupCompleteAction());
  }
  // yield put(loginSuccess())
}
function* _createCategory(action) {
  const language = yield select(makeSelectLocale());
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.createCategoryTicket(rest);
    if (res.success) {
      if (language === "en") {
        notificationBar("Create category successful.");
      } else {
        notificationBar("Tạo danh mục thành công.");
      }
      callback && callback();
      yield put(createCategoryCompleteAction(res.data));
    } else {
      yield put(createCategoryCompleteAction());
    }
  } catch (error) {
    yield put(createCategoryCompleteAction());
  }
  // yield put(loginSuccess())
}
function* _fetchCategory(action) {
  try {
    let res = yield window.connection.fetchCategoryTicket({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchCategoryCompleteAction({
          data: res.data.items,
          totalPage: res.data.pagination ? res.data.pagination.totalCount : 1,
        })
      );
    } else {
      yield put(fetchCategoryCompleteAction());
    }
  } catch (error) {
    yield put(fetchCategoryCompleteAction());
  }
  // yield put(loginSuccess())
}
function* _deleteCategory(action) {
  const language = yield select(makeSelectLocale());
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.deleteCategoryTicket(rest);
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete category successful.");
      } else {
        notificationBar("Xóa danh mục thành công.");
      }
      callback && callback();
    } else {
      yield put(deleteCategoryCompleteAction());
    }
  } catch (error) {
    yield put(deleteCategoryCompleteAction());
  }
  // yield put(loginSuccess())
}
function* _updateCategory(action) {
  const language = yield select(makeSelectLocale());
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.updateCategoryTicket(rest);
    if (res.success) {
      if (language === "en") {
        notificationBar("Update category successful.");
      } else {
        notificationBar("Cập nhật danh mục thành công.");
      }
      callback && callback();
    } else {
      yield put(updateCategoryCompleteAction());
    }
  } catch (error) {
    yield put(updateCategoryCompleteAction());
  }
  // yield put(loginSuccess())
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(FETCH_AUTH_GROUP, _fetchAuthGroup),
    takeLatest(CREATE_CATEGORY, _createCategory),
    takeLatest(FETCH_CATEGORY, _fetchCategory),
    takeLatest(DELETE_CATEGORY, _deleteCategory),
    takeLatest(UPDATE_CATEGORY, _updateCategory),
  ]);
}
