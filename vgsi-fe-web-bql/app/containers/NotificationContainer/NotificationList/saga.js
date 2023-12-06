import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { notificationBar, parseTree } from "../../../utils";
import {
  DELETE_NOTIFICATION_ACTION,
  FETCH_ALL_NOTIFICATIOIN_ACTION,
  FETCH_NOTIFICATION_CATEGORY_ACTION,
} from "./constants";
import {
  fetchNotificationCompleteAction,
  fetchCategoryNotificationCompleteAction,
  deleteNotificationCompleteAction,
} from "./actions";

function* _fetchAllNotification(action) {
  try {
    let res = yield window.connection.fetchAllNotification({
      ...action.payload,
      // type_in: 0,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchNotificationCompleteAction({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchNotificationCompleteAction());
    }
  } catch (error) {
    yield put(fetchNotificationCompleteAction());
  }
}

function* _fetchNotificationCategory(action) {
  try {
    let res = yield window.connection.fetchNotificationCategory({
      ...action.payload,
      // type: 0,
      pageSize: 2000,
    });
    if (res.success) {
      yield put(fetchCategoryNotificationCompleteAction(res.data.items));
    } else {
      yield put(fetchCategoryNotificationCompleteAction());
    }
  } catch (error) {
    yield put(fetchCategoryNotificationCompleteAction());
  }
}

function* _deleteNotification(action) {
  try {
    const { callback, message, ...rest } = action.payload;
    let res = yield window.connection.deleteNotification(rest);
    if (res.success) {
      message && notificationBar(message);
      callback && callback();
    } else {
      yield put(deleteNotificationCompleteAction());
    }
  } catch (error) {
    yield put(deleteNotificationCompleteAction());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_NOTIFICATION_CATEGORY_ACTION, _fetchNotificationCategory),
    takeLatest(FETCH_ALL_NOTIFICATIOIN_ACTION, _fetchAllNotification),
    takeLatest(DELETE_NOTIFICATION_ACTION, _deleteNotification),
  ]);
}
