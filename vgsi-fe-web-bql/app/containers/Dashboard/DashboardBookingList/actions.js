/*
 *
 * DashboardBookingList actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ALL_BOOKING,
  FETCH_ALL_BOOKING_COMPLETE,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_SERVICE_FREE,
  FETCH_SERVICE_FREE_COMPLETE,
  FETCH_DETAIL_SERVICE,
  FETCH_DETAIL_SERVICE_COMPLETE,
  FETCH_SLOT_FREE,
  FETCH_SLOT_FREE_COMPLETE,
  CREATE_BOOKING,
  CREATE_BOOKING_COMPLETE,
  FETCH_ALL_CONFIG,
  FETCH_ALL_CONFIG_COMPLETE,
  CHECK_PRICE_BOOKING,
  CHECK_PRICE_BOOKING_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchAllBookingAction(payload) {
  return {
    type: FETCH_ALL_BOOKING,
    payload,
  };
}
export function fetchAllBookingCompleteAction(payload) {
  return {
    type: FETCH_ALL_BOOKING_COMPLETE,
    payload,
  };
}

export function fetchApartmentAction(payload) {
  return {
    type: FETCH_APARTMENT,
    payload,
  };
}

export function fetchApartmentCompleteAction(payload) {
  return {
    type: FETCH_APARTMENT_COMPLETE,
    payload,
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
    payload,
  };
}

export function fetchDetailService(payload) {
  return {
    type: FETCH_DETAIL_SERVICE,
    payload,
  };
}

export function fetchDetailServiceComplete(payload) {
  return {
    type: FETCH_DETAIL_SERVICE_COMPLETE,
    payload,
  };
}

export function createBooking(payload) {
  return {
    type: CREATE_BOOKING,
    payload,
  };
}
export function createBookingComplete(payload) {
  return {
    type: CREATE_BOOKING_COMPLETE,
    payload,
  };
}

export function fetchSlotFree(payload) {
  return {
    type: FETCH_SLOT_FREE,
    payload,
  };
}
export function fetchSlotFreeComplete(payload) {
  return {
    type: FETCH_SLOT_FREE_COMPLETE,
    payload,
  };
}

export function fetchAllConfig(payload) {
  return {
    type: FETCH_ALL_CONFIG,
    payload,
  };
}

export function fetchAllConfigComplete(payload) {
  return {
    type: FETCH_ALL_CONFIG_COMPLETE,
    payload,
  };
}

export function checkPriceBooking(payload) {
  return {
    type: CHECK_PRICE_BOOKING,
    payload,
  };
}

export function checkPriceBookingComplete(payload) {
  return {
    type: CHECK_PRICE_BOOKING_COMPLETE,
    payload,
  };
}
