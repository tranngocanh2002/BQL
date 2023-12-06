/*
 *
 * StaffList actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ALL_STAFF,
  FETCH_ALL_STAFF_COMPLETE,
  DELETE_STAFF,
  DELETE_STAFF_COMPLETE,
  UPDATE_STAFF,
  UPDATE_STAFF_COMPLETE,
  FETCH_GROUP_AUTH,
  FETCH_GROUP_AUTH_COMPLETE,
  IMPORT_STAFF,
  IMPORT_STAFF_COMPLETE,
  CHANGE_STATUS_STAFF,
  CHANGE_STATUS_STAFF_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}
export function fetchAllStaffAction(payload) {
  return {
    type: FETCH_ALL_STAFF,
    payload,
  };
}
export function fetchAllStaffCompleteAction(payload) {
  return {
    type: FETCH_ALL_STAFF_COMPLETE,
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

export function importStaffAction(payload) {
  return {
    type: IMPORT_STAFF,
    payload,
  };
}

export function importStaffCompleteAction(payload) {
  return {
    type: IMPORT_STAFF_COMPLETE,
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
