/*
 *
 * CombineCardList actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ALL_COMBINE_CARD,
  FETCH_ALL_COMBINE_CARD_COMPLETE,
  DELETE_COMBINE_CARD,
  DELETE_COMBINE_CARD_COMPLETE,
  UPDATE_DETAIL,
  UPDATE_DETAIL_COMPLETE,
  IMPORT_COMBINE_CARD,
  IMPORT_COMBINE_CARD_COMPLETE,
  CREATE_COMBINE_CARD,
  CREATE_COMBINE_CARD_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}
export function fetchAllCombineCardAction(payload) {
  return {
    type: FETCH_ALL_COMBINE_CARD,
    payload,
  };
}
export function fetchAllCombineCardCompleteAction(payload) {
  return {
    type: FETCH_ALL_COMBINE_CARD_COMPLETE,
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

export function importCombineCard(payload) {
  return {
    type: IMPORT_COMBINE_CARD,
    payload,
  };
}
export function importCombineCardComplete(payload) {
  return {
    type: IMPORT_COMBINE_CARD_COMPLETE,
    payload,
  };
}
export function createCombineCardAction(payload) {
  return {
    type: CREATE_COMBINE_CARD,
    payload,
  };
}
export function createCombineCardCompleteAction(payload) {
  return {
    type: CREATE_COMBINE_CARD_COMPLETE,
    payload,
  };
}
