/*
 *
 * BillCreate actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_APARTMENT,
  FTECH_APARTMENT_COMPLETE,
  FETCH_FILTER_FEE,
  FETCH_FILTER_FEE_COMPLETE,
  RESET_FILTER_FEE, CREATE_BILL_COMPLETE, CREATE_BILL
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
    type: FTECH_APARTMENT_COMPLETE,
    payload
  };
}

export function fetchFilterFee(payload) {
  return {
    type: FETCH_FILTER_FEE,
    payload
  };
}

export function fetchFilterFeeComplete(payload) {
  return {
    type: FETCH_FILTER_FEE_COMPLETE,
    payload
  };
}

export function createBill(payload) {
  return {
    type: CREATE_BILL,
    payload
  };
}

export function createBillComplete(payload) {
  return {
    type: CREATE_BILL_COMPLETE,
    payload
  };
}

export function resetFilterFee() {
  return {
    type: RESET_FILTER_FEE,
  };
}
