import { all, put, select, takeLatest } from "redux-saga/effects";
import { notificationBar } from "../../../utils";
import {
  CREATE_NOTIFICATION_CATEGORY_ACTION,
  FETCH_NOTIFICATION_CATEGORY_ACTION,
  UPDATE_NOTIFICATION_CATEGORY_ACTION,
  DELETE_NOTIFICATION_CATEGORY_ACTION,
} from "./constants";
import {
  createCategoryNotificationCompleteAction,
  fetchCategoryNotificationCompleteAction,
  updateCategoryNotificationCompleteAction,
  deleteCategoryNotificationCompleteAction,
} from "./actions";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _createNotificationCategory(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.createNotificationCategory(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Create notification category successful.");
      } else {
        notificationBar("Tạo danh mục thông báo thành công.");
      }
      callback && callback();
      yield put(createCategoryNotificationCompleteAction(true));
    } else {
      yield put(createCategoryNotificationCompleteAction(false));
    }
  } catch (error) {
    yield put(createCategoryNotificationCompleteAction(false));
  }
}

function* _updateNotificationCategory(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.updateNotificationCategory(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Edit notification category successful.");
      } else {
        notificationBar("Sửa danh mục thông báo thành công.");
      }
      callback && callback();
      yield put(updateCategoryNotificationCompleteAction(true));
    } else {
      yield put(updateCategoryNotificationCompleteAction(false));
    }
  } catch (error) {
    yield put(updateCategoryNotificationCompleteAction(false));
  }
}

function* _deleteNotificationCategory(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.deleteNotificationCategory(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete notification category successful.");
      } else {
        notificationBar("Xóa danh mục thông báo thành công.");
      }
      callback && callback();
      yield put(deleteCategoryNotificationCompleteAction(true));
    } else {
      yield put(deleteCategoryNotificationCompleteAction(false));
    }
  } catch (error) {
    yield put(deleteCategoryNotificationCompleteAction(false));
  }
}

function* _fetchNotificationCategory(action) {
  try {
    let res = yield window.connection.fetchNotificationCategory({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchCategoryNotificationCompleteAction({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchCategoryNotificationCompleteAction());
    }
  } catch (error) {
    yield put(fetchCategoryNotificationCompleteAction());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(
      CREATE_NOTIFICATION_CATEGORY_ACTION,
      _createNotificationCategory
    ),
    takeLatest(FETCH_NOTIFICATION_CATEGORY_ACTION, _fetchNotificationCategory),
    takeLatest(
      UPDATE_NOTIFICATION_CATEGORY_ACTION,
      _updateNotificationCategory
    ),
    takeLatest(
      DELETE_NOTIFICATION_CATEGORY_ACTION,
      _deleteNotificationCategory
    ),
  ]);
}
