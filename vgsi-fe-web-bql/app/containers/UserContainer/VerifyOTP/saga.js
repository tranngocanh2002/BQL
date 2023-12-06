import { VERIFY_OTP } from "./constants";

import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { verifyOTPCompleteAction } from "./actions";
import { notification } from "antd";
import { saveToken } from "../../../redux/actions/config";
import { notificationBar } from "utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _verifyOTP(action) {
  try {
    const { otp, token } = action.payload;
    const language = yield select(makeSelectLocale());
    let res = yield window.connection.verifyOTP({ otp, token });
    if (res.success) {
      yield put(verifyOTPCompleteAction(true));
    } else {
      if (language === "en") {
        notificationBar("Invalid OTP.");
      } else {
        notificationBar("Mã OTP không hợp lệ.");
      }
      yield put(verifyOTPCompleteAction(false));
    }
  } catch (error) {
    yield put(verifyOTPCompleteAction(false));
  }
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([takeLatest(VERIFY_OTP, _verifyOTP)]);
}
