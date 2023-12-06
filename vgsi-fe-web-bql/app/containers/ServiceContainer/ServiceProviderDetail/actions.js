/*
 *
 * ServiceProviderDetail actions
 *
 */

import { DEFAULT_ACTION, FETCH_DETAIL_SERVICE_PROVIDER, FETCH_DETAIL_SERVICE_PROVIDER_COMPLETE, UPDATE_SERVICE_PROVIDER, UPDATE_SERVICE_PROVIDER_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchDetailServiceProvider(payload) {
  return {
    type: FETCH_DETAIL_SERVICE_PROVIDER,
    payload
  };
}

export function fetchDetailServiceProviderComplete(payload) {
  return {
    type: FETCH_DETAIL_SERVICE_PROVIDER_COMPLETE,
    payload
  };
}

export function updateServiceProvider(payload) {
  return {
    type: UPDATE_SERVICE_PROVIDER,
    payload
  };
}

export function updateServiceProviderComplete(payload) {
  return {
    type: UPDATE_SERVICE_PROVIDER_COMPLETE,
    payload
  };
}
