/*
 *
 * DashboardBills actions
 *
 */

import {
  DEFAULT_ACTION, FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE,
  FETCH_ALL_BILLS, FETCH_ALL_BILLS_COMPLETE,
  FETCH_BUILDING_AREA, FETCH_BUILDING_AREA_COMPLETE
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
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
export function fetchBuildingAreaAction(payload) {
  return {
    type: FETCH_BUILDING_AREA,
    payload
  };
}

export function fetchBuildingAreaCompleteAction(payload) {
  return {
    type: FETCH_BUILDING_AREA_COMPLETE,
    payload
  };
}

export function fetchAllBills(payload) {
  return {
    type: FETCH_ALL_BILLS,
    payload
  };
}

export function fetchAllBillsComplete(payload) {
  return {
    type: FETCH_ALL_BILLS_COMPLETE,
    payload
  };
}