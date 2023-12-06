/*
 *
 * DashboardInvoiceBill actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_ALL_FEE_OF_APARTMENT,
  FETCH_ALL_FEE_OF_APARTMENT_COMPLETE,
  CREATE_ORDER,
  CREATE_ORDER_COMPLETE,
  CLEAR_FORM,
  FETCH_MEMBER,
  FETCH_MEMBER_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchApartment(payload) {
  return {
    type: FETCH_APARTMENT,
    payload,
  };
}

export function fetchApartmentComplete(payload) {
  return {
    type: FETCH_APARTMENT_COMPLETE,
    payload,
  };
}

export function fetchMemberAction(payload) {
  return {
    type: FETCH_MEMBER,
    payload,
  };
}

export function fetchMemberCompleteAction(payload) {
  return {
    type: FETCH_MEMBER_COMPLETE,
    payload,
  };
}

export function fetchFeeOfApartment(payload) {
  return {
    type: FETCH_ALL_FEE_OF_APARTMENT,
    payload,
  };
}

export function fetchFeeOfApartmentComplete(payload) {
  return {
    type: FETCH_ALL_FEE_OF_APARTMENT_COMPLETE,
    payload,
  };
}

export function createOrder(payload) {
  return {
    type: CREATE_ORDER,
    payload,
  };
}

export function createOrderComplete(payload) {
  return {
    type: CREATE_ORDER_COMPLETE,
    payload,
  };
}

export function clearForm(payload) {
  return {
    type: CLEAR_FORM,
    payload,
  };
}
