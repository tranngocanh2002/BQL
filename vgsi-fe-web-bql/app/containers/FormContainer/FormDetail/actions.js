/*
 *
 * FormDetail actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_DETAIL_FORM,
  FETCH_DETAIL_FORM_COMPLETE,
  UPDATE_DETAIL,
  UPDATE_DETAIL_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchDetailFormAction(payload) {
  return {
    type: FETCH_DETAIL_FORM,
    payload,
  };
}

export function fetchDetailFormCompleteAction(payload) {
  return {
    type: FETCH_DETAIL_FORM_COMPLETE,
    payload,
  };
}

export function updateDetailAction(payload) {
  return {
    type: UPDATE_DETAIL,
    payload,
  };
}
export function updateDetailCompleteAction(payload) {
  return {
    type: UPDATE_DETAIL_COMPLETE,
    payload,
  };
}
