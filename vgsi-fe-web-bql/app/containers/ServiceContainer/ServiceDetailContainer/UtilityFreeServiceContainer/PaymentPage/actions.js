/*
 *
 * PaymentPage actions
 *
 */

import { DEFAULT_ACTION, FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE, CREATE_PAYMENT, CREATE_PAYMENT_COMPLETE, FETCH_ALL_PAYMENT, FETCH_ALL_PAYMENT_COMPLETE, DELETE_PAYMENT, DELETE_PAYMENT_COMPLETE, UPDATE_PAYMENT, UPDATE_PAYMENT_COMPLETE, IMPORT_PAYMENT, IMPORT_PAYMENT_COMPLETE } from "./constants";

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

export function createPayment(payload) {
  return {
    type: CREATE_PAYMENT,
    payload
  };
}

export function createPaymentComplete(payload) {
  return {
    type: CREATE_PAYMENT_COMPLETE,
    payload
  };
}

export function fetchAllPayment(payload) {
  return {
    type: FETCH_ALL_PAYMENT,
    payload
  };
}

export function fetchAllPaymentComplete(payload) {
  return {
    type: FETCH_ALL_PAYMENT_COMPLETE,
    payload
  };
}

export function deletePayment(payload) {
  return {
    type: DELETE_PAYMENT,
    payload
  };
}

export function deletePaymentComplete(payload) {
  return {
    type: DELETE_PAYMENT_COMPLETE,
    payload
  };
}

export function updatePayment(payload) {
  return {
    type: UPDATE_PAYMENT,
    payload
  };
}

export function updatePaymentComplete(payload) {
  return {
    type: UPDATE_PAYMENT_COMPLETE,
    payload
  };
}

export function importPayment(payload) {
  return {
    type: IMPORT_PAYMENT,
    payload
  };
}

export function importPaymentComplete(payload) {
  return {
    type: IMPORT_PAYMENT_COMPLETE,
    payload
  };
}
