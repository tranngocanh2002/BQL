/*
 *
 * DashboardDebtAll actions
 *
 */

import {
  DEFAULT_ACTION, FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE,
  FETCH_BUILDING_AREA,
  FETCH_BUILDING_AREA_COMPLETE,
  FETCH_DEBT,
  FETCH_DEBT_COMPLETE
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchApartmentAction(payload) {
  return {
    type: FETCH_APARTMENT,
    payload
  };
}

export function fetchApartmentCompleteAction(payload) {
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

export function fetchDebt(payload) {
  return {
    type: FETCH_DEBT,
    payload
  };
}

export function fetchDebtComplete(payload) {
  return {
    type: FETCH_DEBT_COMPLETE,
    payload
  };
}
