import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_CATEGORY,
  ADD_CATEGORY,
  EDIT_CATEGORY,
  DELETE_CATEGORY,
  ADD_HANDBOOK_ITEM,
  FETCH_HANDBOOK_ITEM,
  EDIT_HANDBOOK_ITEM,
  DELETE_HANDBOOK_ITEM,
} from "./constants";
import {
  fetchCategoryComplete,
  fetchCategory,
  addCategoryComplete,
  editCategoryComplete,
  deleteCategoryComplete,
  fetchHandbookComplete,
  fetchHandbook,
  addHandbookComplete,
  editHandbookComplete,
  deleteHandbookComplete,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchCategory(action) {
  try {
    let res = yield window.connection.fetchResidentHandbookCategory({
      pageSize: 10000,
      sort: "order",
    });
    if (res.success) {
      yield put(fetchCategoryComplete(res.data.items));
    } else {
      yield put(fetchCategoryComplete());
    }
  } catch (error) {
    // yield put(fetchCategoryComplete())
  }
}

function* _fetchHandbook(action) {
  try {
    let res = yield window.connection.fetchResidentHandbook({
      pageSize: 10000,
      sort: "order",
    });
    if (res.success) {
      yield put(fetchHandbookComplete(res.data.items));
    } else {
      yield put(fetchHandbookComplete());
    }
  } catch (error) {
    // yield put(fetchCategoryComplete())
  }
}

function* _addCategory(action) {
  try {
    let res = yield window.connection.addResidentHandbookCategory(
      action.payload
    );
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Create category successful.");
      } else {
        notificationBar("Tạo danh mục thành công.");
      }
      yield put(fetchCategory());
      yield put(addCategoryComplete(true));
    } else {
      yield put(addCategoryComplete());
    }
  } catch (error) {
    yield put(addCategoryComplete());
  }
}
function* _addHandbook(action) {
  try {
    let res = yield window.connection.addResidentHandbook(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Create successful.");
      } else {
        notificationBar("Tạo thành công.");
      }
      yield put(fetchHandbook());
      yield put(addHandbookComplete(true));
    } else {
      yield put(addHandbookComplete());
    }
  } catch (error) {
    yield put(addHandbookComplete());
  }
}

function* _editCategory(action) {
  try {
    let res = yield window.connection.updateResidentHandbookCategory(
      action.payload
    );
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update category successful.");
      } else {
        notificationBar("Cập nhật danh mục thành công.");
      }
      yield put(fetchCategory());
      yield put(editCategoryComplete(true));
    } else {
      yield put(editCategoryComplete());
    }
  } catch (error) {
    yield put(editCategoryComplete());
  }
}

function* _editHandbook(action) {
  try {
    let res = yield window.connection.updateResidentHandbook(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update successful.");
      } else {
        notificationBar("Cập nhật thành công.");
      }
      yield put(fetchHandbook());
      yield put(editHandbookComplete(true));
    } else {
      yield put(editHandbookComplete());
    }
  } catch (error) {
    yield put(editHandbookComplete());
  }
}
function* _deleteCategory(action) {
  try {
    let res = yield window.connection.deleteResidentHandbookCategory(
      action.payload
    );
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete category successful.");
      } else {
        notificationBar("Xóa danh mục thành công.");
      }
      yield put(fetchCategory());
      yield put(deleteCategoryComplete(true));
    } else {
      yield put(deleteCategoryComplete());
    }
  } catch (error) {
    yield put(deleteCategoryComplete());
  }
}
function* _deleteHandbook(action) {
  try {
    let res = yield window.connection.deleteResidentHandbook(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete successful.");
      } else {
        notificationBar("Xóa thành công.");
      }
      yield put(fetchHandbook());
      yield put(deleteHandbookComplete(true));
    } else {
      yield put(deleteHandbookComplete());
    }
  } catch (error) {
    yield put(deleteHandbookComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_CATEGORY, _fetchCategory),
    takeLatest(ADD_CATEGORY, _addCategory),
    takeLatest(EDIT_CATEGORY, _editCategory),
    takeLatest(DELETE_CATEGORY, _deleteCategory),

    takeLatest(ADD_HANDBOOK_ITEM, _addHandbook),
    takeLatest(FETCH_HANDBOOK_ITEM, _fetchHandbook),
    takeLatest(EDIT_HANDBOOK_ITEM, _editHandbook),
    takeLatest(DELETE_HANDBOOK_ITEM, _deleteHandbook),
  ]);
}
