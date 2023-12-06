/*
 *
 * NotificationDetail actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_APARTMENT_FEE_REMINDER,
  FETCH_APARTMENT_FEE_REMINDER_COMPLETE,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  CREATE_NOTIFICATION_FEE_REMINDER,
  CREATE_NOTIFICATION_FEE_REMINDER_COMPLETE,
  FETCH_CATEGORY,
  FETCH_CATEGORY_COMPLETE,
  FETCH_NOTIFICATION_DETAIL,
  FETCH_NOTIFICATION_DETAIL_COMPLETE,
  FETCH_APARTMENT_SENT,
  FETCH_APARTMENT_SENT_COMPLETE,
  FETCH_SURVEY_ANSWER_COMPLETE,
  FETCH_SURVEY_ANSWER,
  FETCH_REPORT_CHART,
  FETCH_REPORT_CHART_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
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

export function fetchSurveyAnswer(payload) {
  return {
    type: FETCH_SURVEY_ANSWER,
    payload,
  };
}

export function fetchSurveyAnswerComplete(payload) {
  return {
    type: FETCH_SURVEY_ANSWER_COMPLETE,
    payload,
  };
}

export function fetchReportChart(payload) {
  return {
    type: FETCH_REPORT_CHART,
    payload,
  };
}

export function fetchReportChartComplete(payload) {
  return {
    type: FETCH_REPORT_CHART_COMPLETE,
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

export function createNotificationDetailReminder(payload) {
  return {
    type: CREATE_NOTIFICATION_FEE_REMINDER,
    payload,
  };
}

export function createNotificationDetailReminderComplete(payload) {
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

export function fetchNotificationDetail(payload) {
  return {
    type: FETCH_NOTIFICATION_DETAIL,
    payload,
  };
}

export function fetchNotificationDetailComplete(payload) {
  return {
    type: FETCH_NOTIFICATION_DETAIL_COMPLETE,
    payload,
  };
}
