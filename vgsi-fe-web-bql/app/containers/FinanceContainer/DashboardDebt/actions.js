/*
 *
 * DashboardDebt actions
 *
 */

import { DEFAULT_ACTION, FETCH_ALL_BUILDING_AREA, FETCH_DETAIL_DEBT_AREA, FETCH_DETAIL_DEBT_AREA_COMPLETE, FETCH_ALL_BUILDING_AREA_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchAllBuildingArea(payload) {
  return {
    type: FETCH_ALL_BUILDING_AREA,
    payload
  };
}

export function fetchAllBuildingAreaComplete(payload) {
  return {
    type: FETCH_ALL_BUILDING_AREA_COMPLETE,
    payload
  };
}

export function fetchDetailDebtArea(payload) {
  return {
    type: FETCH_DETAIL_DEBT_AREA,
    payload
  };
}

export function fetchDetailDebtAreaComplete(payload) {
  return {
    type: FETCH_DETAIL_DEBT_AREA_COMPLETE,
    payload
  };
}
