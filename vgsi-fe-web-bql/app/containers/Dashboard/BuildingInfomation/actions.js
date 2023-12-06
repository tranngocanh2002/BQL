/*
 *
 * BuildingInfomation actions
 *
 */

import { DEFAULT_ACTION, FETCH_ALL_SERVICE, FETCH_ALL_SERVICE_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}


export function fetchAllService(payload) {
  return {
    type: FETCH_ALL_SERVICE,
    payload
  };
}

export function fetchAllServiceComplete(payload) {
  return {
    type: FETCH_ALL_SERVICE_COMPLETE,
    payload
  };
}
