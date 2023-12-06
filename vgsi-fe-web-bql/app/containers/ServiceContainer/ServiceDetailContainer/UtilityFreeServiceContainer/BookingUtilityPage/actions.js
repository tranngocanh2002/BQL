/*
 *
 * BookingUtilityPage actions
 *
 */

import {
  DEFAULT_ACTION, FETCH_ALL_CONFIG, FETCH_ALL_CONFIG_COMPLETE,
  FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE,
  CREATE_BOOKING, CREATE_BOOKING_COMPLETE, FETCH_BOOKING, FETCH_BOOKING_COMPLETE, FETCH_SLOT_FREE, FETCH_SLOT_FREE_COMPLETE
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchAllConfig(payload) {
  return {
    type: FETCH_ALL_CONFIG,
    payload
  };
}

export function fetchAllConfigComplete(payload) {
  return {
    type: FETCH_ALL_CONFIG_COMPLETE,
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

export function createBooking(payload) {
  return {
    type: CREATE_BOOKING,
    payload
  };
}
export function createBookingComplete(payload) {
  return {
    type: CREATE_BOOKING_COMPLETE,
    payload
  };
}

export function fetchBooking(payload) {
  return {
    type: FETCH_BOOKING,
    payload
  };
}
export function fetchBookingComplete(payload) {
  return {
    type: FETCH_BOOKING_COMPLETE,
    payload
  };
}

export function fetchSlotFree(payload) {
  return {
    type: FETCH_SLOT_FREE,
    payload
  };
}
export function fetchSlotFreeComplete(payload) {
  return {
    type: FETCH_SLOT_FREE_COMPLETE,
    payload
  };
}
