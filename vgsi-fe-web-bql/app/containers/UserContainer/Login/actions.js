/*
 *
 * Login actions
 *
 */

import { DEFAULT_ACTION, LOGIN, LOGIN_SUCCESS, LOGIN_FAILED, GET_CAPTCHA, GET_CAPTCHA_COMPLETE, LOGIN_TOKEN } from './constants';

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}
export function loginAction(payload) {
  return {
    type: LOGIN,
    payload
  };
}
export function loginTokenAction(payload) {
  return {
    type: LOGIN_TOKEN,
    payload
  };
}
export function loginSuccess(payload) {
  return {
    type: LOGIN_SUCCESS,
    payload
  };
}
export function loginFailed(payload) {
  return {
    type: LOGIN_FAILED,
    payload
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
