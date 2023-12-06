/*
 *
 * InfomationManagementClusterPage actions
 *
 */

import { DEFAULT_ACTION, FETCH_SERVICE_PROVIDER, FETCH_SERVICE_PROVIDER_COMPLETE, UPDATE_SERVICE_DETAIL, UPDATE_SERVICE_DETAIL_COMPLETE } from "./constants";

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
