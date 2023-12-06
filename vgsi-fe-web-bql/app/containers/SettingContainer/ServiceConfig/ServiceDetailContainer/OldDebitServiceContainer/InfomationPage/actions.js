/*
 *
 * InfomationOldDebitPage actions
 *
 */

import { DEFAULT_ACTION, FETCH_SERVICE_PROVIDER, FETCH_SERVICE_PROVIDER_COMPLETE, UPDATE_SERVICE_DETAIL, UPDATE_SERVICE_DETAIL_COMPLETE, FETCH_OLD_DEBIT_CONFIG, FETCH_OLD_DEBIT_CONFIG_COMPLETE, UPDATE_OLD_DEBIT_CONFIG, UPDATE_OLD_DEBIT_CONFIG_COMPLETE } from "./constants";

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


export function fetchOldDebitConfig(payload) {
  return {
    type: FETCH_OLD_DEBIT_CONFIG,
    payload
  };
}

export function fetchOldDebitConfigComplete(payload) {
  return {
    type: FETCH_OLD_DEBIT_CONFIG_COMPLETE,
    payload
  };
}


export function updateOldDebitConfig(payload) {
  return {
    type: UPDATE_OLD_DEBIT_CONFIG,
    payload
  };
}

export function updateOldDebitConfigComplete(payload) {
  return {
    type: UPDATE_OLD_DEBIT_CONFIG_COMPLETE,
    payload
  };
}
