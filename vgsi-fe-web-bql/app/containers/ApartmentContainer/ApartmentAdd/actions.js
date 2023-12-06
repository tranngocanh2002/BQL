/*
 *
 * ApartmentAdd actions
 *
 */

import { DEFAULT_ACTION, FETCH_BUILDING_AREA, FETCH_BUILDING_AREA_COMPLETE, CREATE_APARTMENT, CREATE_APARTMENT_COMPLETE, FETCH_DETAIL_APARTMENT, FETCH_DETAIL_APARTMENT_COMPLETE, FETCH_ALL_APARTMENT_TYPE, FETCH_ALL_APARTMENT_TYPE_COMPLETE, FETCH_ALL_RESIDENT_BY_PHONE, FETCH_ALL_RESIDENT_BY_PHONE_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchBuildingAreaAction() {
  return {
    type: FETCH_BUILDING_AREA
  };
}
export function fetchBuildingAreaCompleteAction(payload) {
  return {
    type: FETCH_BUILDING_AREA_COMPLETE,
    payload
  };
}
export function createApartmentAction(payload) {
  return {
    type: CREATE_APARTMENT,
    payload
  };
}
export function createApartmentCompleteAction(payload) {
  return {
    type: CREATE_APARTMENT_COMPLETE,
    payload
  };
}
export function fetchDetailApartmentAction(payload) {
  return {
    type: FETCH_DETAIL_APARTMENT,
    payload
  };
}
export function fetchDetailApartmentCompleteAction(payload) {
  return {
    type: FETCH_DETAIL_APARTMENT_COMPLETE,
    payload
  };
}

export function fetchAllApartmentType(payload) {
  return {
    type: FETCH_ALL_APARTMENT_TYPE,
    payload
  };
}
export function fetchAllApartmentTypeComplete(payload) {
  return {
    type: FETCH_ALL_APARTMENT_TYPE_COMPLETE,
    payload
  };
}

export function fetchAllResidentByPhoneAction(payload) {
  return {
    type: FETCH_ALL_RESIDENT_BY_PHONE,
    payload
  };
}
export function fetchAllResidentByPhoneCompleteAction(payload) {
  return {
    type: FETCH_ALL_RESIDENT_BY_PHONE_COMPLETE,
    payload
  };
}
