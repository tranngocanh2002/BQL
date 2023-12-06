/*
 *
 * Login reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  LOGIN,
  LOGIN_SUCCESS,
  LOGIN_FAILED,
  GET_CAPTCHA,
  GET_CAPTCHA_COMPLETE,
  LOGIN_TOKEN,
} from "./constants";

export const initialState = fromJS({
  isLogining: false,
  success: false,
  countCallFall: 0,
  confirmCode: "",
  captchaImage: undefined,
  captcha: {
    loading: false,
    data: undefined,
  },
});

function loginReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case LOGIN:
      return state.set("isLogining", true);
    case LOGIN_TOKEN:
      return state.set("isLogining", true);
    case LOGIN_SUCCESS:
      return state.set("isLogining", false).set("success", true);
    case LOGIN_FAILED:
      return state
        .set("isLogining", false)
        .set("success", false)
        .set(
          "countCallFall",
          !!action.payload ? action.payload.countCallFailed : 0
        )
        .set(
          "captchaImage",
          !!action.payload ? action.payload.captchaImage : undefined
        );
    case GET_CAPTCHA: {
      return state.setIn(["captcha", "loading"], true);
    }
    case GET_CAPTCHA_COMPLETE: {
      return state
        .setIn(["captcha", "loading"], false)
        .setIn(["captcha", "data"], action.payload);
    }
    default:
      return state;
  }
}

export default loginReducer;
