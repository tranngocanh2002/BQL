/*
 *
 * ServiceProvider actions
 *
 */

import { DEFAULT_ACTION, FETCH_PROVIDERS, FETCH_PROVIDERS_COMPLETE, DELETE_SERVICE_PROVIDER, DELETE_SERVICE_PROVIDER_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}
export function fetchProvidersAction(payload) {
  return {
    type: FETCH_PROVIDERS,
    payload
  };
}
export function fetchProvidersCompleteAction(payload) {
  return {
    type: FETCH_PROVIDERS_COMPLETE,
    payload
  };
}
export function deleteServiceProviderAction(payload) {
  return {
    type: DELETE_SERVICE_PROVIDER,
    payload
  };
}
export function deleteServiceProviderCompleteAction(payload) {
  return {
    type: DELETE_SERVICE_PROVIDER_COMPLETE,
    payload
  };
}
