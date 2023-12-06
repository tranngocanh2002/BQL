/*
 *
 * SetupFeeElectricPage actions
 *
 */

import { DEFAULT_ACTION, FETCH_SERVICE_PROVIDER, FETCH_SERVICE_PROVIDER_COMPLETE, UPDATE_SERVICE_DETAIL, UPDATE_SERVICE_DETAIL_COMPLETE, FETCH_FEE_LEVEL, FETCH_FEE_LEVEL_COMPLETE, CREATE_FEE_LEVEL, CREATE_FEE_LEVEL_COMPLETE, UPDATE_FEE_LEVEL, UPDATE_FEE_LEVEL_COMPLETE, DELETE_FEE_LEVEL } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}


export function fetchElectricFeeLevel(payload) {
  return {
    type: FETCH_FEE_LEVEL,
    payload
  };
}

export function fetchElectricFeeLevelComplete(payload) {
  return {
    type: FETCH_FEE_LEVEL_COMPLETE,
    payload
  };
}


export function createElectricFeeLevel(payload) {
  return {
    type: CREATE_FEE_LEVEL,
    payload
  };
}

export function createElectricFeeLevelComplete(payload) {
  return {
    type: CREATE_FEE_LEVEL_COMPLETE,
    payload
  };
}


export function updateElectricFeeLevel(payload) {
  return {
    type: UPDATE_FEE_LEVEL,
    payload
  };
}

export function updateElectricFeeLevelComplete(payload) {
  return {
    type: UPDATE_FEE_LEVEL_COMPLETE,
    payload
  };
}

export function deleteElectricFeeLevel(payload) {
  return {
    type: DELETE_FEE_LEVEL,
    payload
  };
}

