/*
 *
 * InfoUsage actions
 *
 */

import { DEFAULT_ACTION, FETCH_USAGE, FETCH_USAGE_COMPLETE, FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE, IMPORT_USAGE, IMPORT_USAGE_COMPLETE, ADD_INFO, ADD_INFO_COMPLETE, UPDATE_INFO, DELETE_INFO_COMPLETE, UPDATE_INFO_COMPLETE, DELETE_INFO } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchUsage(payload) {
  return {
    type: FETCH_USAGE,
    payload
  };
}

export function fetchUsageComplete(payload) {
  return {
    type: FETCH_USAGE_COMPLETE,
    payload
  };
}

export function fetchApartment(payload) {
  return {
    type: FETCH_APARTMENT,
    payload
  };
}

export function fetchApartmentComplete(payload) {
  return {
    type: FETCH_APARTMENT_COMPLETE,
    payload
  };
}

export function importUsage(payload) {
  return {
    type: IMPORT_USAGE,
    payload
  };
}

export function importUsageComplete(payload) {
  return {
    type: IMPORT_USAGE_COMPLETE,
    payload
  };
}

export function addInfo(payload) {
  return {
    type: ADD_INFO,
    payload
  };
}

export function addInfoComplete(payload) {
  return {
    type: ADD_INFO_COMPLETE,
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

export function deleteInfo(payload) {
  return {
    type: DELETE_INFO,
    payload
  };
}

export function deleteInfoComplete(payload) {
  return {
    type: DELETE_INFO_COMPLETE,
    payload
  };
}
