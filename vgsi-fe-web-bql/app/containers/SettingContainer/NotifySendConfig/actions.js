/*
 *
 * Config Send Notify actions
 * 
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ALL_NOTIFY_SEND_CONFIG,
  FETCH_ALL_NOTIFY_SEND_CONFIG_COMPLETE,
  UPDATE_NOTIFY_SEND_CONFIG,
  UPDATE_NOTIFY_SEND_CONFIG_COMPLETE,
  UPDATE_ALL_NOTIFY_SEND_CONFIG,
  UPDATE_ALL_NOTIFY_SEND_CONFIG_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchNotifySendConfig(payload) {
  return {
    type: FETCH_ALL_NOTIFY_SEND_CONFIG,
    payload,
  };
}

export function fetchNotifySendConfigComplete(payload) {
  return {
    type: FETCH_ALL_NOTIFY_SEND_CONFIG_COMPLETE,
    payload,
  };
}

export function updateNotifySendConfig(payload) {
  return {
    type: UPDATE_NOTIFY_SEND_CONFIG,
    payload,
  };
}

export function updateNotifySendConfigComplete(payload) {
  return {
    type: UPDATE_NOTIFY_SEND_CONFIG_COMPLETE,
    payload,
  };
}

export function updateAllNotifySendConfig(payload) {
  return {
    type: UPDATE_ALL_NOTIFY_SEND_CONFIG,
    payload,
  };
}

export function updateAllNotifySendConfigComplete(payload) {
  return {
    type: UPDATE_ALL_NOTIFY_SEND_CONFIG_COMPLETE,
    payload,
  };
}
