/*
 *
 * CashBook actions
 *
 */

import { DEFAULT_ACTION, FETCH_ALL_BILL, FETCH_ALL_BILL_COMPLETE, FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE, FETCH_BUILDING_AREA, FETCH_BUILDING_AREA_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchAllBill(payload) {
	return {
		type: FETCH_ALL_BILL,
		payload
	};
}

export function fetchAllBillComplete(payload) {
	return {
		type: FETCH_ALL_BILL_COMPLETE,
		payload
	};
}

export function fetchApartmentAction(payload){
  return {
    type: FETCH_APARTMENT,
    payload
  };
}

export function fetchApartmentCompleteAction(payload){
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