/*
 *
 * LockFeeTemplatePage actions
 *
 */

import { DEFAULT_ACTION, FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE, CREATE_PAYMENT, CREATE_PAYMENT_COMPLETE, FETCH_ALL_PAYMENT, FETCH_ALL_PAYMENT_COMPLETE, DELETE_PAYMENT, DELETE_PAYMENT_COMPLETE, UPDATE_PAYMENT, UPDATE_PAYMENT_COMPLETE, IMPORT_PAYMENT, IMPORT_PAYMENT_COMPLETE, APPROVE_PAYMENT, APPROVE_PAYMENT_COMPLETE, FETCH_LAST_MONTH_FEE, FETCH_LAST_MONTH_FEE_COMPLETE, FETCH_DESCRIPTION_FEE, FETCH_DESCRIPTION_FEE_COMPLETE, CLEAR_CACHE_MODAL, FETCH_VEHICLE, FETCH_VEHICLE_COMPLETE } from "./constants";

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
export function fetchVehicle(payload) {
  return {
    type: FETCH_VEHICLE,
    payload
  };
}

export function fetchVehicleComplete(payload) {
  return {
    type: FETCH_VEHICLE_COMPLETE,
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

export function approvePayment(payload) {
  return {
    type: APPROVE_PAYMENT,
    payload
  };
}

export function approvePaymentComplete(payload) {
  return {
    type: APPROVE_PAYMENT_COMPLETE,
    payload
  };
}

export function fetchLastMonthFee(payload) {
  return {
    type: FETCH_LAST_MONTH_FEE,
    payload
  };
}

export function fetchLastMonthFeeComplete(payload) {
  return {
    type: FETCH_LAST_MONTH_FEE_COMPLETE,
    payload
  };
}
export function fetchDescriptionFee(payload) {
  return {
    type: FETCH_DESCRIPTION_FEE,
    payload
  };
}

export function fetchDescriptionFeeComplete(payload) {
  return {
    type: FETCH_DESCRIPTION_FEE_COMPLETE,
    payload
  };
}

export function clearCacheModal(payload) {
  return {
    type: CLEAR_CACHE_MODAL,
    payload
  };
}
