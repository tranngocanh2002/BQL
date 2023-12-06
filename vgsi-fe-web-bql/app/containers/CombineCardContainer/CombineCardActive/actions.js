/*
 *
 * CardActive actions
 *
 */

import {
  CREATE_ACTIVE_CARD,
  CREATE_ACTIVE_CARD_COMPLETE,
  DEFAULT_ACTION,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_MEMBER,
  FETCH_MEMBER_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
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
export function createActiveCard(payload) {
  return {
    type: CREATE_ACTIVE_CARD,
    payload,
  };
}
export function createActiveCardComplete(payload) {
  return {
    type: CREATE_ACTIVE_CARD_COMPLETE,
    payload,
  };
}
