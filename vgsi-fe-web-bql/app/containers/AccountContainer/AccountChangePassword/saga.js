import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { FETCH_DETAIL, UPDATE_INFO, CHANGE_PASSWORD } from "./constants";

import {
  fetchDetailComplete,
  updateInfoComplete,
  changePasswordComplete,
} from "./actions";
import { logout } from "../../../redux/actions/config";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { notificationBar } from "utils";

function* _changePass(action) {
  try {
    let res = yield window.connection.changePasswordUser(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      // if (language === "en") {
      //   notificationBar("Change password successfully.");
      // } else {
      //   notificationBar("Thay đổi mật khẩu thành công.");
      // }
      // yield put(logout());
      yield put(changePasswordComplete(true));
    } else {
      yield put(changePasswordComplete());
    }
  } catch (error) {
    yield put(changePasswordComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([takeLatest(CHANGE_PASSWORD, _changePass)]);
}
