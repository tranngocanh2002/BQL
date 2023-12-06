import {
  DEFAULT_ACTION,
  FETCH_DETAIL_COMBINE_CARD,
  FETCH_DETAIL_COMBINE_CARD_COMPLETE,
  DELETE_COMBINE_CARD,
  DELETE_COMBINE_CARD_COMPLETE,
  UPDATE_DETAIL,
  UPDATE_DETAIL_COMPLETE,
  CHANGE_COMBINE_CARD_STATUS_ERROR,
  CHANGE_COMBINE_CARD_STATUS_COMPLETE,
  CHANGE_COMBINE_CARD_STATUS,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_MEMBER_COMPLETE,
  FETCH_MEMBER,
  CREATE_ACTIVE_CARD,
  CREATE_ACTIVE_CARD_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}
export function fetchDetailCombineCardAction(payload) {
  return {
    type: FETCH_DETAIL_COMBINE_CARD,
    payload,
  };
}
export function fetchDetailCombineCardCompleteAction(payload) {
  return {
    type: FETCH_DETAIL_COMBINE_CARD_COMPLETE,
    payload,
  };
}
export function deleteCombineCardAction(payload) {
  return {
    type: DELETE_COMBINE_CARD,
    payload,
  };
}
export function deleteCombineCardCompleteAction(payload) {
  return {
    type: DELETE_COMBINE_CARD_COMPLETE,
    payload,
  };
}

export function updateCombineCardAction(payload) {
  return {
    type: UPDATE_DETAIL,
    payload,
  };
}

export function updateCombineCardCompleteAction(payload) {
  return {
    type: UPDATE_DETAIL_COMPLETE,
    payload,
  };
}
export function changeCombineCardStatusAction(payload) {
  return {
    type: CHANGE_COMBINE_CARD_STATUS,
    payload,
  };
}

export function changeCombineCardStatusCompleteAction(payload) {
  return {
    type: CHANGE_COMBINE_CARD_STATUS_COMPLETE,
    payload,
  };
}

export function changeCombineCardStatusErrorAction() {
  return {
    type: CHANGE_COMBINE_CARD_STATUS_ERROR,
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
