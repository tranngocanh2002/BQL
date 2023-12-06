/*
 *
 * AccountChangePassword actions
 *
 */

import { DEFAULT_ACTION, CHANGE_PASSWORD, CHANGE_PASSWORD_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function changePassword(payload) {
  return {
    type: CHANGE_PASSWORD,
    payload
  };
}

export function changePasswordComplete(payload) {
  return {
    type: CHANGE_PASSWORD_COMPLETE,
    payload
  };
}
