/*
 *
 * ServiceBookingFeeList actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ALL_BOOKING_FEE,
  FETCH_ALL_BOOKING_FEE_COMPLETE,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_SERVICE_FREE,
  FETCH_SERVICE_FREE_COMPLETE,
  FETCH_DETAIL_SERVICE,
  FETCH_DETAIL_SERVICE_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchAllBookingFeeAction(payload) {
  return {
    type: FETCH_ALL_BOOKING_FEE,
    payload
  };
}

export function fetchAllBookingFeeCompleteAction(payload) {
  return {
    type: FETCH_ALL_BOOKING_FEE_COMPLETE,
    payload
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

export function fetchServiceFreeAction(payload) {
  return {
    type: FETCH_SERVICE_FREE,
    payload,
  };
}

export function fetchServiceFreeCompleteAction(payload) {
  return {
    type: FETCH_SERVICE_FREE_COMPLETE,
    payload
  };
}

export function fetchDetailService(payload) {
  return {
    type: FETCH_DETAIL_SERVICE,
    payload
  };
}

export function fetchDetailServiceComplete(payload) {
  return {
    type: FETCH_DETAIL_SERVICE_COMPLETE,
    payload
  };
}



