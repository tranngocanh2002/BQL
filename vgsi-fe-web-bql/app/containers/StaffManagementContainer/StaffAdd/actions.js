/*
 *
 * StaffAdd actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_GROUP_AUTH,
  FETCH_GROUP_AUTH_COMPLETE,
  CREATE_STAFF,
  CREATE_STAFF_COMPLETE,
  UPDATE_STAFF,
  UPDATE_STAFF_COMPLETE,
  FETCH_DETAIL,
  FETCH_DETAIL_COMPLETE,
  UPDATE_STAFF_AND_USERDETAIL,
  UPDATE_STAFF_AND_USERDETAIL_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}
export function fetchGroupAuthAction() {
  return {
    type: FETCH_GROUP_AUTH,
  };
}
export function fetchGroupAuthCompleteAction(payload) {
  return {
    type: FETCH_GROUP_AUTH_COMPLETE,
    payload,
  };
}
export function createStaffAction(payload) {
  return {
    type: CREATE_STAFF,
    payload,
  };
}
export function createStaffCompleteAction(payload) {
  return {
    type: CREATE_STAFF_COMPLETE,
    payload,
  };
}
export function updateStaffAction(payload) {
  return {
    type: UPDATE_STAFF,
    payload,
  };
}
export function updateStaffCompleteAction(payload) {
  return {
    type: UPDATE_STAFF_COMPLETE,
    payload,
  };
}
export function fetchDetailAction(payload) {
  return {
    type: FETCH_DETAIL,
    payload,
  };
}
export function fetchDetailCompleteAction(payload) {
  return {
    type: FETCH_DETAIL_COMPLETE,
    payload,
  };
}
export function updateStaffAndDetail(payload) {
  return {
    type: UPDATE_STAFF_AND_USERDETAIL,
    payload,
  };
}
export function updateStaffAndDetailComplete(payload) {
  return {
    type: UPDATE_STAFF_AND_USERDETAIL_COMPLETE,
    payload,
  };
}
