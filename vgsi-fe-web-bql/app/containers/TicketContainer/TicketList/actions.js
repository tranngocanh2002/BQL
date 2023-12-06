/*
 *
 * TicketList actions
 *
 */

import { DEFAULT_ACTION, FETCH_ALL_TICKET, FETCH_ALL_TICKET_COMPLETE, FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE, FETCH_CATEGORY, FETCH_CATEGORY_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchAllTicketAction(payload) {
  return {
    type: FETCH_ALL_TICKET,
    payload
  };
}

export function fetchAllTicketCompleteAction(payload) {
  return {
    type: FETCH_ALL_TICKET_COMPLETE,
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

export function fetchCategoryAction(payload) {
  return {
    type: FETCH_CATEGORY,
    payload
  };
}

export function fetchCategoryCompleteAction(payload) {
  return {
    type: FETCH_CATEGORY_COMPLETE,
    payload
  };
}

