/*
 *
 * NotificationDetail actions
 *
 */

import { DEFAULT_ACTION, FETCH_DETAIL_NOTIFICATION, FETCH_DETAIL_NOTIFICATION_COMPLETE, UPDATE_NOTIFICATION } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchDetailNotification(payload) {
  return {
    type: FETCH_DETAIL_NOTIFICATION,
    payload
  };
}

export function fetchDetailNotificationComplete(payload) {
  return {
    type: FETCH_DETAIL_NOTIFICATION_COMPLETE,
    payload
  };
}

export function updateNotification(payload) {
  return {
    type: UPDATE_NOTIFICATION,
    payload
  };
}

