/*
 *
 * Register actions
 *
 */

import { DEFAULT_ACTION, GET_CAPTCHA, GET_CAPTCHA_COMPLETE, FORGOT_PASS, FORGOT_PASS_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}
export function getCaptchaAction() {
  return {
    type: GET_CAPTCHA
  };
}
export function getCaptchaCompleteAction(payload) {
  return {
    type: GET_CAPTCHA_COMPLETE,
    payload
  };
}
export function forgotPassAction(payload) {
  return {
    type: FORGOT_PASS,
    payload
  };
}
export function forgotPassCompleteAction(payload) {
  return {
    type: FORGOT_PASS_COMPLETE,
    payload
  };
}
