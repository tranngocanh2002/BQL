/*
 *
 * historyAccessControl actions
 *
 */

import { DEFAULT_ACTION, FETCH_ALL_APARTMENT, FETCH_ALL_APARTMENT_COMPLETE, FETCH_ALL_HISTORY, FETCH_ALL_HISTORY_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}


export function fetchApartment(payload) {
  return {
    type: FETCH_ALL_APARTMENT,
    payload
  };
}

export function fetchApartmentComplete(payload) {
  return {
    type: FETCH_ALL_APARTMENT_COMPLETE,
    payload
  };
}


export function fetchAllHistory(payload) {
  return {
    type: FETCH_ALL_HISTORY,
    payload
  };
}

export function fetchAllHistoryComplete(payload) {
  return {
    type: FETCH_ALL_HISTORY_COMPLETE,
    payload
  };
}
