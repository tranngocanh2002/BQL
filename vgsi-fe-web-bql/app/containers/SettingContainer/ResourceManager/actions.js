/*
 *
 * ResourceManager actions
 *
 */

import { DEFAULT_ACTION, FETCH_LOG_EMAIL_COMPLETE, FETCH_LOG_EMAIL, FETCH_LOG_NOTIFICATION, FETCH_LOG_NOTIFICATION_COMPLETE, FETCH_LOG_SMS, FETCH_LOG_SMS_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchLogEmail(payload) {
  return {
    type: FETCH_LOG_EMAIL,
    payload
  };
}

export function fetchLogEmailComplete(payload) {
  return {
    type: FETCH_LOG_EMAIL_COMPLETE,
    payload
  };
}

export function fetchLogNotification(payload) {
  return {
    type: FETCH_LOG_NOTIFICATION,
    payload
  };
}

export function fetchLogNotificationComplete(payload) {
  return {
    type: FETCH_LOG_NOTIFICATION_COMPLETE,
    payload
  };
}

export function fetchLogSMS(payload) {
  return {
    type: FETCH_LOG_SMS,
    payload
  };
}

export function fetchLogSMSComplete(payload) {
  return {
    type: FETCH_LOG_SMS_COMPLETE,
    payload
  };
}
