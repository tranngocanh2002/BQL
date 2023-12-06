/*
 *
 * Register reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  GET_CAPTCHA,
  GET_CAPTCHA_COMPLETE,
  FORGOT_PASS,
  FORGOT_PASS_COMPLETE,
  VERIFY_OTP,
  VERIFY_OTP_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  success: false,
});

function verifyOTPReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case VERIFY_OTP: {
      return state.setIn(["verifyOTP", "loading"], true);
    }
    case VERIFY_OTP_COMPLETE: {
      return state
        .setIn(["verifyOTP", "loading"], false)
        .setIn(["verifyOTP", "success"], true);
    }
    default:
      return state;
  }
}

export default verifyOTPReducer;
