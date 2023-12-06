/*
 *
 * TaskEdit actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ALL_STAFF,
  FETCH_ALL_STAFF_COMPLETE,
  FETCH_DETAIL_TASK,
  FETCH_DETAIL_TASK_COMPLETE,
  UPDATE_TASK,
  UPDATE_TASK_COMPLETE
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

export function fetchDetailTaskAction(payload) {
  return {
    type: FETCH_DETAIL_TASK,
    payload,
  };
}

export function fetchDetailTaskCompleteAction(payload) {
  return {
    type: FETCH_DETAIL_TASK_COMPLETE,
    payload,
  };
}

export function updateTask(payload) {
  return {
    type: UPDATE_TASK,
    payload,
  };
}
export function updateTaskComplete(payload) {
  return {
    type: UPDATE_TASK_COMPLETE,
    payload,
  };
}
