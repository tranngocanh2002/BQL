/*
 *
 * ResidentList actions
 *
 */

import {
  DEFAULT_ACTION, FETCH_ALL_RESIDENT,
  FETCH_ALL_RESIDENT_COMPLETE,
  UPDATE_DETAIL, UPDATE_DETAIL_COMPLETE,
  DELETE_RESIDENT, DELETE_RESIDENT_COMPLETE, FETCH_APARTMENT_OF_RESIDENT, FETCH_APARTMENT_OF_RESIDENT_COMPLETE, IMPORT_RESIDENT, IMPORT_RESIDENT_COMPLETE
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchAllResidentAction(payload) {
  return {
    type: FETCH_ALL_RESIDENT,
    payload
  };
}
export function fetchAllResidentCompleteAction(payload) {
  return {
    type: FETCH_ALL_RESIDENT_COMPLETE,
    payload
  };
}
export function deleteResidentAction(payload) {
  return {
    type: DELETE_RESIDENT,
    payload
  };
}
export function deleteResidentCompleteAction(payload) {
  return {
    type: DELETE_RESIDENT_COMPLETE,
    payload
  };
}

export function updateDetailAction(payload) {
  return {
    type: UPDATE_DETAIL,
    payload
  };
}
export function updateDetailCompleteAction(payload) {
  return {
    type: UPDATE_DETAIL_COMPLETE,
    payload
  };
}

export function fetchApartmentOfResidentAction(payload) {
  return {
    type: FETCH_APARTMENT_OF_RESIDENT,
    payload
  };
}
export function fetchApartmentOfResidentCompleteAction(payload) {
  return {
    type: FETCH_APARTMENT_OF_RESIDENT_COMPLETE,
    payload
  };
}

export function importResident(payload) {
  return {
    type: IMPORT_RESIDENT,
    payload
  };
}
export function importResidentComplete(payload) {
  return {
    type: IMPORT_RESIDENT_COMPLETE,
    payload
  };
}