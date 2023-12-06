/*
 *
 * TaskAdd actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ALL_STAFF,
  FETCH_ALL_STAFF_COMPLETE,
  CREATE_TASK,
  CREATE_TASK_COMPLETE,
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

export function createTask(payload) {
  return {
    type: CREATE_TASK,
    payload,
  };
}
export function createTaskComplete(payload) {
  return {
    type: CREATE_TASK_COMPLETE,
    payload,
  };
}
