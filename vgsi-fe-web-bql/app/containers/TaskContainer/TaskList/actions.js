/*
 *
 * TaskList actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ALL_STAFF,
  FETCH_ALL_STAFF_COMPLETE,
  FETCH_ALL_TASK,
  FETCH_ALL_TASK_COMPLETE,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_CATEGORY,
  FETCH_CATEGORY_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchAllTaskAction(payload) {
  return {
    type: FETCH_ALL_TASK,
    payload,
  };
}

export function fetchAllTaskCompleteAction(payload) {
  return {
    type: FETCH_ALL_TASK_COMPLETE,
    payload,
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
