/*
 *
 * VerifyOTP actions
 *
 */

import { DEFAULT_ACTION, VERIFY_OTP, VERIFY_OTP_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function verifyOTPAction(payload) {
  return {
    type: VERIFY_OTP,
    payload,
  };
}

export function verifyOTPCompleteAction(payload) {
  return {
    type: VERIFY_OTP_COMPLETE,
    payload,
  };
}
