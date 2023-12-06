/*
 *
 * BookingDetail actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_DETAIL_BOOKING,
  FETCH_DETAIL_BOOKING_COMPLETE,
  FETCH_SERVICE_FREE,
  FETCH_SERVICE_FREE_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchDetailBookingAction(payload) {
  return {
    type: FETCH_DETAIL_BOOKING,
    payload,
  };
}

export function fetchDetailBookingCompleteAction(payload) {
  return {
    type: FETCH_DETAIL_BOOKING_COMPLETE,
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
