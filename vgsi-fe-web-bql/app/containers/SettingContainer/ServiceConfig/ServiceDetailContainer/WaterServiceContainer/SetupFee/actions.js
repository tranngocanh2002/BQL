/*
 *
 * SetupFeeWaterPage actions
 *
 */

import { DEFAULT_ACTION, FETCH_SERVICE_PROVIDER, FETCH_SERVICE_PROVIDER_COMPLETE, UPDATE_SERVICE_DETAIL, UPDATE_SERVICE_DETAIL_COMPLETE, FETCH_FEE_LEVEL, FETCH_FEE_LEVEL_COMPLETE, CREATE_FEE_LEVEL, CREATE_FEE_LEVEL_COMPLETE, UPDATE_FEE_LEVEL, UPDATE_FEE_LEVEL_COMPLETE, DELETE_FEE_LEVEL } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}


export function fetchWaterFeeLevel(payload) {
  return {
    type: FETCH_FEE_LEVEL,
    payload
  };
}

export function fetchWaterFeeLevelComplete(payload) {
  return {
    type: FETCH_FEE_LEVEL_COMPLETE,
    payload
  };
}


export function createWaterFeeLevel(payload) {
  return {
    type: CREATE_FEE_LEVEL,
    payload
  };
}

export function createWaterFeeLevelComplete(payload) {
  return {
    type: CREATE_FEE_LEVEL_COMPLETE,
    payload
  };
}


export function updateWaterFeeLevel(payload) {
  return {
    type: UPDATE_FEE_LEVEL,
    payload
  };
}

export function updateWaterFeeLevelComplete(payload) {
  return {
    type: UPDATE_FEE_LEVEL_COMPLETE,
    payload
  };
}

export function deleteWaterFeeLevel(payload) {
  return {
    type: DELETE_FEE_LEVEL,
    payload
  };
}

