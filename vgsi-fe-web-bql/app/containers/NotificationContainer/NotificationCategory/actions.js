/*
 *
 * NotificationCategory actions
 *
 */

import {
  DEFAULT_ACTION, CREATE_NOTIFICATION_CATEGORY_ACTION,
  CREATE_NOTIFICATION_CATEGORY_COMPLETE_ACTION,
  FETCH_NOTIFICATION_CATEGORY_ACTION, FETCH_NOTIFICATION_CATEGORY_COMPLETE, UPDATE_NOTIFICATION_CATEGORY_ACTION, UPDATE_NOTIFICATION_CATEGORY_COMPLETE, DELETE_NOTIFICATION_CATEGORY_ACTION, DELETE_NOTIFICATION_CATEGORY_COMPLETE
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}
export function createCategoryNotificationAction(payload) {
  return {
    type: CREATE_NOTIFICATION_CATEGORY_ACTION,
    payload
  };
}
export function createCategoryNotificationCompleteAction(payload) {
  return {
    type: CREATE_NOTIFICATION_CATEGORY_COMPLETE_ACTION,
    payload
  };
}
export function fetchCategoryNotificationAction(payload) {
  return {
    type: FETCH_NOTIFICATION_CATEGORY_ACTION,
    payload
  };
}
export function fetchCategoryNotificationCompleteAction(payload) {
  return {
    type: FETCH_NOTIFICATION_CATEGORY_COMPLETE,
    payload
  };
}
export function updateCategoryNotificationAction(payload) {
  return {
    type: UPDATE_NOTIFICATION_CATEGORY_ACTION,
    payload
  };
}
export function updateCategoryNotificationCompleteAction(payload) {
  return {
    type: UPDATE_NOTIFICATION_CATEGORY_COMPLETE,
    payload
  };
}
export function deleteCategoryNotificationAction(payload) {
  return {
    type: DELETE_NOTIFICATION_CATEGORY_ACTION,
    payload
  };
}
export function deleteCategoryNotificationCompleteAction(payload) {
  return {
    type: DELETE_NOTIFICATION_CATEGORY_COMPLETE,
    payload
  };
}
