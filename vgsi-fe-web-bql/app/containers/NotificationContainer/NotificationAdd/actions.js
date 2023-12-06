/*
 *
 * NotificationAdd actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  CREATE_NOTIFICATION_FEE_REMINDER,
  CREATE_NOTIFICATION_FEE_REMINDER_COMPLETE,
  FETCH_CATEGORY,
  FETCH_CATEGORY_COMPLETE,
  FETCH_BUILDING_AREA_ACTION,
  FETCH_BUILDING_AREA_COMPLETE_ACTION,
  FETCH_APARTMENT_SENT,
  FETCH_APARTMENT_SENT_COMPLETE,
  FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  SHOW_CHOOSE_TEMPLATE_LIST,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchAnnouncementFeeTemplate(payload) {
  return {
    type: FETCH_ANNOUNCEMENT_TEMPLATE_FEE,
    payload,
  };
}

export function fetchAnnouncementFeeTemplateComplete(payload) {
  return {
    type: FETCH_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
    payload,
  };
}

export function fetchAllAnnouncementFeeTemplate(payload) {
  return {
    type: FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE,
    payload,
  };
}

export function fetchAllAnnouncementFeeTemplateComplete(payload) {
  return {
    type: FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
    payload,
  };
}

//XXX: 3 Action generator to create notification
export function createNotificationAddReminder(payload) {
  return {
    type: CREATE_NOTIFICATION_FEE_REMINDER,
    payload,
  };
}

export function createNotificationAddReminderComplete(payload) {
  return {
    type: CREATE_NOTIFICATION_FEE_REMINDER_COMPLETE,
    payload,
  };
}

export function fetchCategory(payload) {
  return {
    type: FETCH_CATEGORY,
    payload,
  };
}

export function fetchCategoryComplete(payload) {
  return {
    type: FETCH_CATEGORY_COMPLETE,
    payload,
  };
}

export function fetchBuildingAreaAction() {
  return {
    type: FETCH_BUILDING_AREA_ACTION,
  };
}

export function fetchBuildingAreaCompleteAction(payload) {
  return {
    type: FETCH_BUILDING_AREA_COMPLETE_ACTION,
    payload,
  };
}

export function fetchApartmentSent(payload) {
  return {
    type: FETCH_APARTMENT_SENT,
    payload,
  };
}

export function fetchApartmentSentComplete(payload) {
  return {
    type: FETCH_APARTMENT_SENT_COMPLETE,
    payload,
  };
}

export function showChooseTemplateList(payload) {
  return {
    type: SHOW_CHOOSE_TEMPLATE_LIST,
    payload,
  };
}
