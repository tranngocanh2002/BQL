/*
 *
 * ApartmentList actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ALL_APARTMENT,
  FETCH_ALL_APARTMENT_COMPLETE,
  DELETE_APARTMENT,
  DELETE_APARTMENT_COMPLETE,
  FETCH_BUILDING_AREA,
  FETCH_BUILDING_AREA_COMPLETE,
  UPDATE_DETAIL,
  UPDATE_DETAIL_COMPLETE,
  IMPORT_APARTMENT,
  IMPORT_APARTMENT_COMPLETE,
  FETCH_ALL_APARTMENT_TYPE,
  FETCH_ALL_APARTMENT_TYPE_COMPLETE,
  FETCH_ALL_RESIDENT_BY_PHONE,
  FETCH_ALL_RESIDENT_BY_PHONE_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}
export function fetchAllApartmentAction(payload) {
  return {
    type: FETCH_ALL_APARTMENT,
    payload,
  };
}
export function fetchAllApartmentCompleteAction(payload) {
  return {
    type: FETCH_ALL_APARTMENT_COMPLETE,
    payload,
  };
}
export function deleteApartmentAction(payload) {
  return {
    type: DELETE_APARTMENT,
    payload,
  };
}
export function deleteApartmentCompleteAction(payload) {
  return {
    type: DELETE_APARTMENT_COMPLETE,
    payload,
  };
}

export function updateApartmentAction(payload) {
  return {
    type: UPDATE_DETAIL,
    payload,
  };
}

export function updateApartmentCompleteAction(payload) {
  return {
    type: UPDATE_DETAIL_COMPLETE,
    payload,
  };
}

export function fetchBuildingAreaAction() {
  return {
    type: FETCH_BUILDING_AREA,
  };
}
export function fetchBuildingAreaCompleteAction(payload) {
  return {
    type: FETCH_BUILDING_AREA_COMPLETE,
    payload,
  };
}

export function importApartment(payload) {
  return {
    type: IMPORT_APARTMENT,
    payload,
  };
}
export function importApartmentComplete(payload) {
  return {
    type: IMPORT_APARTMENT_COMPLETE,
    payload,
  };
}
export function fetchAllApartmentType(payload) {
  return {
    type: FETCH_ALL_APARTMENT_TYPE,
    payload,
  };
}
export function fetchAllApartmentTypeComplete(payload) {
  return {
    type: FETCH_ALL_APARTMENT_TYPE_COMPLETE,
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
