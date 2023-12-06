/*
 *
 * DetailUltilityPage actions
 *
 */

import {
  DEFAULT_ACTION, ADD_ULTILITY_ITEM_COMPLETE, ADD_ULTILITY_ITEM,
  UPDATE_ULTILITY_ITEM, UPDATE_ULTILITY_ITEM_COMPLETE, FETCH_DETAIL, FETCH_DETAIL_COMPLETE
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function addUltilityItem(payload) {
  return {
    type: ADD_ULTILITY_ITEM,
    payload
  };
}

export function addUltilityItemComlete(payload) {
  return {
    type: ADD_ULTILITY_ITEM_COMPLETE,
    payload
  };
}

export function updateUltilityItem(payload) {
  return {
    type: UPDATE_ULTILITY_ITEM,
    payload
  };
}

export function updateUltilityItemComlete(payload) {
  return {
    type: UPDATE_ULTILITY_ITEM_COMPLETE,
    payload
  };
}

export function fetchDetailUltilityItem(payload) {
  return {
    type: FETCH_DETAIL,
    payload
  };
}

export function fetchDetailUltilityItemComlete(payload) {
  return {
    type: FETCH_DETAIL_COMPLETE,
    payload
  };
}

