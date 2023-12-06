/*
 *
 * ServiceAdd actions
 *
 */

import { DEFAULT_ACTION, FETCH_DETAIL_SERVICE_CLOUD, FETCH_DETAIL_SERVICE_CLOUD_COMPLETE, FETCH_SERVICE_PROVIDER, FETCH_SERVICE_PROVIDER_COMPLETE, ADD_SERVICE, ADD_SERVICE_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchDetailServiceCloud(payload) {
  return {
    type: FETCH_DETAIL_SERVICE_CLOUD,
    payload
  };
}

export function fetchDetailServiceCloudComplete(payload) {
  return {
    type: FETCH_DETAIL_SERVICE_CLOUD_COMPLETE,
    payload
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

export function addService(payload) {
  return {
    type: ADD_SERVICE,
    payload
  };
}

export function addServiceComplete(payload) {
  return {
    type: ADD_SERVICE_COMPLETE,
    payload
  };
}
