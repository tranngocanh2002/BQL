/*
 *
 * ResidentOldList actions
 *
 */

import {
  DEFAULT_ACTION, FETCH_ALL_OLD_RESIDENT,
  FETCH_ALL_OLD_RESIDENT_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchAllOldResidentAction(payload) {
  return {
    type: FETCH_ALL_OLD_RESIDENT,
    payload
  };
}
export function fetchAllOldResidentCompleteAction(payload) {
  return {
    type: FETCH_ALL_OLD_RESIDENT_COMPLETE,
    payload
  };
}

