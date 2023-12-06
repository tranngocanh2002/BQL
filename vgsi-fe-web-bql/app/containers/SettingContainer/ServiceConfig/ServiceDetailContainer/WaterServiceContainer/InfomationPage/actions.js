/*
 *
 * InfomationWaterPage actions
 *
 */

import { DEFAULT_ACTION, FETCH_SERVICE_PROVIDER, FETCH_SERVICE_PROVIDER_COMPLETE, UPDATE_SERVICE_DETAIL, UPDATE_SERVICE_DETAIL_COMPLETE, FETCH_WATER_CONFIG, FETCH_WATER_CONFIG_COMPLETE, UPDATE_WATER_CONFIG, UPDATE_WATER_CONFIG_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}


export function fetchServiceProvider(payload) {
  return {
    type: FETCH_SERVICE_PROVIDER,
    payload
  };
}

export function fetchServiceProviderComplete(payload) {
  return {
    type: FETCH_SERVICE_PROVIDER_COMPLETE,
    payload
  };
}


export function updateServiceDetail(payload) {
  return {
    type: UPDATE_SERVICE_DETAIL,
    payload
  };
}

export function updateServiceDetailComplete(payload) {
  return {
    type: UPDATE_SERVICE_DETAIL_COMPLETE,
    payload
  };
}


export function fetchWaterConfig(payload) {
  return {
    type: FETCH_WATER_CONFIG,
    payload
  };
}

export function fetchWaterConfigComplete(payload) {
  return {
    type: FETCH_WATER_CONFIG_COMPLETE,
    payload
  };
}


export function updateWaterConfig(payload) {
  return {
    type: UPDATE_WATER_CONFIG,
    payload
  };
}

export function updateWaterConfigComplete(payload) {
  return {
    type: UPDATE_WATER_CONFIG_COMPLETE,
    payload
  };
}
