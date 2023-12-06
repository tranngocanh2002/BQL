/*
 *
 * ServiceCloud actions
 *
 */

import { DEFAULT_ACTION, FETCH_ALL_SERVICE_CLOUD, FETCH_ALL_SERVICE_CLOUD_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchAllServiceCloud() {
  return {
    type: FETCH_ALL_SERVICE_CLOUD
  };
}

export function fetchAllServiceCloudComplete(payload) {
  return {
    type: FETCH_ALL_SERVICE_CLOUD_COMPLETE,
    payload
  };
}
