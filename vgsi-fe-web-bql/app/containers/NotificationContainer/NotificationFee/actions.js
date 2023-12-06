/*
 *
 * NotificationFee actions
 *
 */

import {
  DEFAULT_ACTION,
  SHOW_CHOOSE_TEMPLATE_LIST,
  FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  FETCH_APARTMENT_FEE_REMINDER,
  FETCH_APARTMENT_FEE_REMINDER_COMPLETE,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  CREATE_NOTIFICATION_FEE_REMINDER,
  CREATE_NOTIFICATION_FEE_REMINDER_COMPLETE,
  FETCH_CATEGORY,
  FETCH_CATEGORY_COMPLETE,
  FETCH_NOTIFICATION_TO_PRINT,
  FETCH_NOTIFICATION_TO_PRINT_COMPLETE,
  FETCH_APARTMENT_SENT,
  FETCH_APARTMENT_SENT_COMPLETE,
  FETCH_BUILDING_AREA_ACTION,
  FETCH_BUILDING_AREA_COMPLETE_ACTION,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchApartmentFeeReminder(payload) {
  return {
    type: FETCH_APARTMENT_FEE_REMINDER,
    payload,
  };
}

export function fetchApartmentFeeReminderComplete(payload) {
  return {
    type: FETCH_APARTMENT_FEE_REMINDER_COMPLETE,
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

export function createNotificationFeeReminder(payload) {
  return {
    type: CREATE_NOTIFICATION_FEE_REMINDER,
    payload,
  };
}

export function createNotificationFeeReminderComplete(payload) {
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
export function fetchNotificationToPrint(payload) {
  return {
    type: FETCH_NOTIFICATION_TO_PRINT,
    payload,
  };
}

export function fetchNotificationToPrintComplete(payload) {
  return {
    type: FETCH_NOTIFICATION_TO_PRINT_COMPLETE,
    payload,
  };
}
export function showChooseTemplateList(payload) {
  return {
    type: SHOW_CHOOSE_TEMPLATE_LIST,
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
