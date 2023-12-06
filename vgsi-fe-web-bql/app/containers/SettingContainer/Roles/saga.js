import { FETCH_ALL_GROUP, DELETE_GROUP } from "./constants";
import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  fetchAllGroupComplete,
  deleteGroupComplete,
  fetchAllGroup,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchAllGroup(action) {
  try {
    let res = yield window.connection.getGroupAuth();
    if (res.success) {
      yield put(fetchAllGroupComplete(res.data || []));
    } else {
      yield put(fetchAllGroupComplete([]));
    }
  } catch (error) {
    console.log(error);
    yield put(fetchAllGroupComplete([]));
  }
}
function* _deleteGroup(action) {
  try {
    let res = yield window.connection.deleteGroupAuth(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete roles group successful.");
      } else {
        notificationBar("Xóa nhóm quyền thành công.");
      }
      yield put(fetchAllGroup());
    } else {
      yield put(deleteGroupComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(deleteGroupComplete());
  }
}

// Individual exports for testing
export default function* rolesSaga() {
  yield all([
    takeLatest(FETCH_ALL_GROUP, _fetchAllGroup),
    takeLatest(DELETE_GROUP, _deleteGroup),
  ]);
}
