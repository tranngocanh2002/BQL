/*
 *
 * NotificationList actions
 *
 */

import {
  DEFAULT_ACTION,
  DELETE_NOTIFICATION_ACTION,
  DELETE_NOTIFICATION_COMPLETE_ACTION,
  FETCH_ALL_NOTIFICATIOIN_ACTION,
  FETCH_ALL_NOTIFICATIOIN_COMPLETE_ACTION,
  FETCH_NOTIFICATION_CATEGORY_ACTION,
  FETCH_NOTIFICATION_CATEGORY_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchNotificationAction(payload) {
  return {
    type: FETCH_ALL_NOTIFICATIOIN_ACTION,
    payload,
  };
}

export function fetchNotificationCompleteAction(payload) {
  return {
    type: FETCH_ALL_NOTIFICATIOIN_COMPLETE_ACTION,
    payload,
  };
}

export function fetchCategoryNotificationAction(payload) {
  return {
    type: FETCH_NOTIFICATION_CATEGORY_ACTION,
    payload,
  };
}
export function fetchCategoryNotificationCompleteAction(payload) {
  return {
    type: FETCH_NOTIFICATION_CATEGORY_COMPLETE,
    payload,
  };
}

export function deleteNotificationAction(payload) {
  return {
    type: DELETE_NOTIFICATION_ACTION,
    payload,
  };
}
export function deleteNotificationCompleteAction(payload) {
  return {
    type: DELETE_NOTIFICATION_COMPLETE_ACTION,
    payload,
  };
}
