/*
 *
 * ConfigUtilityPage actions
 *
 */

import { DEFAULT_ACTION, FETCH_ALL_CONFIG, FETCH_ALL_CONFIG_COMPLETE, CREATE_CONFIG, CREATE_CONFIG_COMPLETE, FETCH_CONFIG_PRICE_COMPLETE, FETCH_CONFIG_PRICE, CREATE_CONFIG_PRICE, CREATE_CONFIG_PRICE_COMPLETE, DELETE_CONFIG_PRICE, DELETE_CONFIG_PRICE_COMPLETE, DELETE_CONFIG_PLACE, DELETE_CONFIG_PLACE_COMPLETE, UPDATE_CONFIG, UPDATE_CONFIG_COMPLETE } from "./constants";

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

export function createConfig(payload) {
  return {
    type: CREATE_CONFIG,
    payload
  };
}

export function createConfigComplete(payload) {
  return {
    type: CREATE_CONFIG_COMPLETE,
    payload
  };
}
export function updateConfig(payload) {
  return {
    type: UPDATE_CONFIG,
    payload
  };
}

export function updateConfigComplete(payload) {
  return {
    type: UPDATE_CONFIG_COMPLETE,
    payload
  };
}

export function createConfigPrice(payload) {
  return {
    type: CREATE_CONFIG_PRICE,
    payload
  };
}

export function createConfigPriceComplete(payload) {
  return {
    type: CREATE_CONFIG_PRICE_COMPLETE,
    payload
  };
}

export function deleteConfigPrice(payload) {
  return {
    type: DELETE_CONFIG_PRICE,
    payload
  };
}

export function deleteConfigPriceComplete(payload) {
  return {
    type: DELETE_CONFIG_PRICE_COMPLETE,
    payload
  };
}

export function deleteConfigPlace(payload) {
  return {
    type: DELETE_CONFIG_PLACE,
    payload
  };
}

export function deleteConfigPlaceComplete(payload) {
  return {
    type: DELETE_CONFIG_PLACE_COMPLETE,
    payload
  };
}

export function fetchConfigPrice(payload) {
  return {
    type: FETCH_CONFIG_PRICE,
    payload
  };
}

export function fetchConfigPriceComplete(payload) {
  return {
    type: FETCH_CONFIG_PRICE_COMPLETE,
    payload
  };
}
