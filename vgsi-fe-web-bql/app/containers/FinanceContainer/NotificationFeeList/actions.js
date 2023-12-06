/*
 *
 * NotificationFeeList actions
 *
 */

import { DEFAULT_ACTION, FETCH_ALL_NOTIFICATION_FEE_ACTION, FETCH_ALL_NOTIFICATION_FEE_COMPLETE_ACTION } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchNotificationFeeAction(payload) {
  return {
    type: FETCH_ALL_NOTIFICATION_FEE_ACTION,
    payload
  };
}

export function fetchNotificationFeeCompleteAction(payload) {
  return {
    type: FETCH_ALL_NOTIFICATION_FEE_COMPLETE_ACTION,
    payload
  };
}
