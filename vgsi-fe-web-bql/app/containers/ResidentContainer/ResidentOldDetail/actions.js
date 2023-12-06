/*
 *
 * ResidentOldDetail actions
 *
 */

import { DEFAULT_ACTION, FETCH_DETAIL_OLD_RESIDENT, FETCH_DETAIL_OLD_RESIDENT_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchDetailOldResidentAction(payload) {
  return {
    type: FETCH_DETAIL_OLD_RESIDENT,
    payload
  };
}

export function fetchDetailOldResidentCompleteAction(payload) {
  return {
    type: FETCH_DETAIL_OLD_RESIDENT_COMPLETE,
    payload
  };
}
