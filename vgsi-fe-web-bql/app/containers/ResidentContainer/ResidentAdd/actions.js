/*
 *
 * ResidentAdd actions
 *
 */

import {
  DEFAULT_ACTION,
  CREATE_RESIDENT,
  CREATE_RESIDENT_COMPLETE,
  UPDATE_RESIDENT,
  UPDATE_RESIDENT_COMPLETE,
  FTECH_APARTMENT,
  FTECH_APARTMENT_COMPLETE,
  FETCH_ALL_RESIDENT_BY_PHONE,
  FETCH_ALL_RESIDENT_BY_PHONE_COMPLETE,
  FETCH_DETAIL,
  FETCH_DETAIL_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}
export function createResidentAction(payload) {
  return {
    type: CREATE_RESIDENT,
    payload,
  };
}
export function createResidentCompleteAction(payload) {
  return {
    type: CREATE_RESIDENT_COMPLETE,
    payload,
  };
}
export function updateResidentAction(payload) {
  return {
    type: UPDATE_RESIDENT,
    payload,
  };
}
export function updateResidentCompleteAction(payload) {
  return {
    type: UPDATE_RESIDENT_COMPLETE,
    payload,
  };
}
export function fetchDetailAction(payload) {
  return {
    type: FETCH_DETAIL,
    payload,
  };
}
export function fetchDetailCompleteAction(payload) {
  return {
    type: FETCH_DETAIL_COMPLETE,
    payload,
  };
}
export function fetchApartmentAction(payload) {
  return {
    type: FTECH_APARTMENT,
    payload,
  };
}
export function fetchApartmentCompleteAction(payload) {
  return {
    type: FTECH_APARTMENT_COMPLETE,
    payload,
  };
}

export function fetchAllResidentByPhoneAction(payload) {
  return {
    type: FETCH_ALL_RESIDENT_BY_PHONE,
    payload,
  };
}
export function fetchAllResidentByPhoneCompleteAction(payload) {
  return {
    type: FETCH_ALL_RESIDENT_BY_PHONE_COMPLETE,
    payload,
  };
}
