/*
 *
 * UtilityFreeServiceContainer actions
 *
 */

import { DEFAULT_ACTION, FETCH_DETAIL_SERVICE, FETCH_DETAIL_SERVICE_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchDetailService(payload) {
  return {
    type: FETCH_DETAIL_SERVICE,
    payload
  };
}

export function fetchDetailServiceComplete(payload) {
  return {
    type: FETCH_DETAIL_SERVICE_COMPLETE,
    payload
  };
}
