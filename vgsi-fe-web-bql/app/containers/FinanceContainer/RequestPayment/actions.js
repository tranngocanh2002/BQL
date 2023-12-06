/*
 *
 * RequestPayment actions
 *
 */

import { DEFAULT_ACTION, FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE, FETCH_PAYMENT_REQUEST, FETCH_PAYMENT_REQUEST_COMPLETE, DELETE_REQUEST, DELETE_REQUEST_COMPLETE } from "./constants";

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

export function fetchPaymentRequest(payload) {
  return {
    type: FETCH_PAYMENT_REQUEST,
    payload
  };
}

export function fetchPaymentRequestComplete(payload) {
  return {
    type: FETCH_PAYMENT_REQUEST_COMPLETE,
    payload
  };
}

export function deleteRequest(payload) {
  return {
    type: DELETE_REQUEST,
    payload
  };
}

export function deleteRequestComplete(payload) {
  return {
    type: DELETE_REQUEST_COMPLETE,
    payload
  };
}