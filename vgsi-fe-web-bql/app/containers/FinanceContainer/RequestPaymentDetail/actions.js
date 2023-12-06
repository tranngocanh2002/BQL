/*
 *
 * RequestPaymentDetail actions
 *
 */

import {
  DEFAULT_ACTION,
  DELETE_REQUEST,
  DELETE_REQUEST_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function deleteRequest(payload) {
  return {
    type: DELETE_REQUEST,
    payload,
  };
}

export function deleteRequestComplete(payload) {
  return {
    type: DELETE_REQUEST_COMPLETE,
    payload,
  };
}
