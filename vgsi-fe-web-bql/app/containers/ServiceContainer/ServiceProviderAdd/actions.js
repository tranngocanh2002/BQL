/*
 *
 * ServiceProviderAdd actions
 *
 */

import { DEFAULT_ACTION, CREATE_PROVIDER, CREATE_PROVIDER_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}
export function createProviderAction(payload) {
  return {
    type: CREATE_PROVIDER,
    payload
  };
}
export function createProviderCompleteAction(payload) {
  return {
    type: CREATE_PROVIDER_COMPLETE,
    payload
  };
}
