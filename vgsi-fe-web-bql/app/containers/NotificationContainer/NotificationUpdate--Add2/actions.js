/*
 *
 * NotificationUpdate actions
 *
 */

import { DEFAULT_ACTION, FETCH_CATEGORY_ACTION, FETCH_CATEGORY_COMPLETE_ACTION, FETCH_BUILDING_AREA_ACTION, FETCH_ULTILITY_ACTION, FETCH_BUILDING_AREA_COMPLETE_ACTION, CREATE_NOTIFICATION_ACTION, CREATE_NOTIFICATION_COMPLETE_ACTION, FETCH_TOTAL_APARTMENT_ACTION, FETCH_TOTAL_APARTMENT_COMPLETE_ACTION, UPDATE_NOTIFICATION_ACTION, UPDATE_NOTIFICATION_COMPLETE_ACTION, FETCH_DETAIL_NOTIFICATION, FETCH_DETAIL_NOTIFICATION_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchCategoryAction() {
  return {
    type: FETCH_CATEGORY_ACTION
  };
}

export function fetchCategoryCompleteAction(payload) {
  return {
    type: FETCH_CATEGORY_COMPLETE_ACTION,
    payload
  };
}

export function fetchBuildingAreaAction() {
  return {
    type: FETCH_BUILDING_AREA_ACTION
  };
}

export function fetchUltilityAction() {
  return {
    type: FETCH_ULTILITY_ACTION
  };
}

export function fetchBuildingAreaCompleteAction(payload) {
  return {
    type: FETCH_BUILDING_AREA_COMPLETE_ACTION,
    payload
  };
}

export function createNotificationAction(payload) {
  return {
    type: CREATE_NOTIFICATION_ACTION,
    payload
  };
}

export function createNotificationCompleteAction(payload) {
  return {
    type: CREATE_NOTIFICATION_COMPLETE_ACTION,
    payload
  };
}

export function fetchTotalApartmentAction(payload) {
  return {
    type: FETCH_TOTAL_APARTMENT_ACTION,
    payload
  };
}

export function fetchTotalApartmentCompleteAction(payload) {
  return {
    type: FETCH_TOTAL_APARTMENT_COMPLETE_ACTION,
    payload
  };
}

export function updateNotificationAction(payload) {
  return {
    type: UPDATE_NOTIFICATION_ACTION,
    payload
  };
}

export function updateNotificationCompleteAction(payload) {
  return {
    type: UPDATE_NOTIFICATION_COMPLETE_ACTION,
    payload
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

