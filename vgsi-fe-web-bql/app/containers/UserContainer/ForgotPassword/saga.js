import { GET_CAPTCHA, FORGOT_PASS } from "./constants";

import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  getCaptchaCompleteAction,
  forgotPassCompleteAction,
  getCaptchaAction,
} from "./actions";
import { notification } from "antd";
import { saveToken } from "../../../redux/actions/config";

// function* _getCaptcha(action) {
//   try {
//     let res = yield window.connection.getCaptcha();
//     if (res.success) {
//       yield put(getCaptchaCompleteAction(res.data.captchaImage));
//     } else {
//       yield put(getCaptchaCompleteAction());
//     }
//   } catch (error) {
//     yield put(getCaptchaCompleteAction());
//   }
//   // yield put(loginSuccess())
// }
function* _forgotPassword(action) {
  try {
    const { email, captcha_code } = action.payload;
    let res = yield window.connection.forgotPassword({ email, captcha_code });
    if (res.success) {
      yield put(forgotPassCompleteAction(true));
    } else {
      yield put(forgotPassCompleteAction(false));
      // yield put(getCaptchaAction());
    }
  } catch (error) {
    // yield put(getCaptchaAction());
    yield put(forgotPassCompleteAction(false));
  }
  // yield put(loginSuccess())
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    // takeLatest(GET_CAPTCHA, _getCaptcha),
    takeLatest(FORGOT_PASS, _forgotPassword),
  ]);
}
