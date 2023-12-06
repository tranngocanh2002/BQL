import { GET_CAPTCHA, LOGIN, LOGIN_TOKEN } from "./constants";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { all, put, select, takeLatest } from "redux-saga/effects";
import { saveToken, saveTokenNoti } from "../../redux/actions/config";
import { selectTokenNoti } from "../../redux/selectors/config";
import { notificationBar } from "../../utils";
import { getCaptchaCompleteAction, loginFailed, loginSuccess } from "./actions";

function* _login(action) {
  const { username, password, captcha_code, form, confirmLogin } =
    action.payload;
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.login({
      email: username,
      password,
      // captcha_code,
      confirm_login: confirmLogin,
    });
    if (res.success) {
      yield put(saveToken(res.data));
      let tokenNoti = yield select(selectTokenNoti());
      if (tokenNoti) {
        yield put(saveTokenNoti(tokenNoti));
      }
      if (language === "en") {
        notificationBar("Login successful.");
      } else {
        notificationBar("Đăng nhập thành công.");
      }
      yield put(loginSuccess());
    } else {
      // if (
      //   !!res.data &&
      //   !!res.data.errors &&
      //   !!res.data.errors.captcha_code &&
      //   res.data.errors.captcha_code.length > 0
      // ) {
      //   !!form &&
      //     !!form.resetFields &&
      //     form.setFields({
      //       captcha_code: {
      //         value: "",
      //         errors: [new Error(res.data.errors.captcha_code[0])],
      //       },
      //     });
      // }

      yield put(loginFailed(res.data));
    }
  } catch (error) {
    yield put(loginFailed());
  }
  // yield put(loginSuccess())
}
function* _loginByToken(action) {
  const { token } = action.payload;
  const language = yield select(makeSelectLocale());
  try {
    let res = yield window.connection.refreshNewToken(token);
    if (res.success) {
      yield put(saveToken(res.data));
      if (language === "en") {
        notificationBar("Login successful.");
      } else {
        notificationBar("Đăng nhập thành công.");
      }
      yield put(loginSuccess());
    } else {
      yield put(loginFailed(res.data));
    }
  } catch (error) {
    yield put(loginFailed());
  }
}

function* _getCaptcha(action) {
  try {
    let res = yield window.connection.getCaptcha();
    if (res.success) {
      yield put(getCaptchaCompleteAction(res.data.captchaImage));
    } else {
      yield put(getCaptchaCompleteAction());
    }
  } catch (error) {
    yield put(getCaptchaCompleteAction());
  }
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(LOGIN, _login),
    takeLatest(LOGIN_TOKEN, _loginByToken),
    takeLatest(GET_CAPTCHA, _getCaptcha),
  ]);
}
