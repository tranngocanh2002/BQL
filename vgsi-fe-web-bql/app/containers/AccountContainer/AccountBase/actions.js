/*
 *
 * AccountBase actions
 *
 */

import { DEFAULT_ACTION, FETCH_DETAIL, FETCH_DETAIL_COMPLETE, UPDATE_INFO, UPDATE_INFO_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchDetail(payload) {
  return {
    type: FETCH_DETAIL,
    payload
  };
}

export function fetchDetailComplete(payload) {
  return {
    type: FETCH_DETAIL_COMPLETE,
    payload
  };
}

export function updateInfo(payload) {
  return {
    type: UPDATE_INFO,
    payload
  };
}

export function updateInfoComplete(payload) {
  return {
    type: UPDATE_INFO_COMPLETE,
    payload
  };
}
