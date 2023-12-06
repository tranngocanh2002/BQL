/*
 *
 * ServiceList actions
 *
 */

import { DEFAULT_ACTION_LIST, FETCH_ALL_SERVICE_LIST, FETCH_ALL_SERVICE_LIST_COMPLETE } from "./constants";

export function defaultActionList() {
  return {
    type: DEFAULT_ACTION_LIST
  };
}

export function fetchAllServiceList(payload) {
  return {
    type: FETCH_ALL_SERVICE_LIST,
    payload
  };
}

export function fetchAllServiceListComplete(payload) {
  return {
    type: FETCH_ALL_SERVICE_LIST_COMPLETE,
    payload
  };
}
