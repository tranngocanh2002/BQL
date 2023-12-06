/*
 *
 * CreatePassword actions
 *
 */

import { DEFAULT_ACTION, CHECK_TOKEN, CHECK_TOKEN_COMPLETE, CREATE_PASSWORD, CREATE_PASSWORD_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}
export function checkTokenAction(payload) {
  return {
    type: CHECK_TOKEN,
    payload
  };
}
export function checkTokenCompleteAction(payload) {
  return {
    type: CHECK_TOKEN_COMPLETE,
    payload
  };
}
export function createPasswordAction(payload) {
  return {
    type: CREATE_PASSWORD,
    payload
  };
}
export function createPasswordCompleteAction(payload) {
  return {
    type: CREATE_PASSWORD_COMPLETE,
    payload
  };
}
