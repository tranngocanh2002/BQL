/*
 *
 * FORMList actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ALL_FORM,
  FETCH_ALL_FORM_COMPLETE,
  UPDATE_STATUS_FORM,
  UPDATE_STATUS_FORM_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}
export function fetchAllFormAction(payload) {
  return {
    type: FETCH_ALL_FORM,
    payload,
  };
}
export function fetchAllFormCompleteAction(payload) {
  return {
    type: FETCH_ALL_FORM_COMPLETE,
    payload,
  };
}
export function updateFormStatusAction(payload) {
  return {
    type: UPDATE_STATUS_FORM,
    payload,
  };
}
export function updateFormStatusCompleteAction(payload) {
  return {
    type: UPDATE_STATUS_FORM_COMPLETE,
    payload,
  };
}
