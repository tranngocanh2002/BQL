/*
 *
 * StaffDetail actions
 *
 */

import {
  CHANGE_STATUS_STAFF,
  CHANGE_STATUS_STAFF_COMPLETE,
  DEFAULT_ACTION,
  DELETE_STAFF,
  DELETE_STAFF_COMPLETE,
  FETCH_DETAIL_STAFF,
  FETCH_DETAIL_STAFF_COMPLETE,
  RESET_PASSWORD_STAFF,
  RESET_PASSWORD_STAFF_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}
export function fetchDetailAction(payload) {
  return {
    type: FETCH_DETAIL_STAFF,
    payload,
  };
}
export function fetchDetailCompleteAction(payload) {
  return {
    type: FETCH_DETAIL_STAFF_COMPLETE,
    payload,
  };
}
export function resetPasswordAction(payload) {
  return {
    type: RESET_PASSWORD_STAFF,
    payload,
  };
}
export function resetPasswordCompleteAction(payload) {
  return {
    type: RESET_PASSWORD_STAFF_COMPLETE,
    payload,
  };
}

export function changeStatusStaffAction(payload) {
  return {
    type: CHANGE_STATUS_STAFF,
    payload,
  };
}
export function changeStatusStaffCompleteAction(payload) {
  return {
    type: CHANGE_STATUS_STAFF_COMPLETE,
    payload,
  };
}
export function deleteStaffAction(payload) {
  return {
    type: DELETE_STAFF,
    payload,
  };
}
export function deleteStaffCompleteAction(payload) {
  return {
    type: DELETE_STAFF_COMPLETE,
    payload,
  };
}
