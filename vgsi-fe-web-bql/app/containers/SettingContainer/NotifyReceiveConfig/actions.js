/*
 *
 * Config Receive Notify actions
 * 
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ALL_NOTIFY_RECEIVE_CONFIG,
  FETCH_ALL_NOTIFY_RECEIVE_CONFIG_COMPLETE,
  UPDATE_NOTIFY_RECEIVE_CONFIG,
  UPDATE_NOTIFY_RECEIVE_CONFIG_COMPLETE,
  UPDATE_ALL_NOTIFY_RECEIVE_CONFIG,
  UPDATE_ALL_NOTIFY_RECEIVE_CONFIG_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchNotifyReceiveConfig(payload) {
  return {
    type: FETCH_ALL_NOTIFY_RECEIVE_CONFIG,
    payload,
  };
}

export function fetchNotifyReceiveConfigComplete(payload) {
  return {
    type: FETCH_ALL_NOTIFY_RECEIVE_CONFIG_COMPLETE,
    payload,
  };
}

export function updateNotifyReceiveConfig(payload) {
  return {
    type: UPDATE_NOTIFY_RECEIVE_CONFIG,
    payload,
  };
}

export function updateNotifyReceiveConfigComplete(payload) {
  return {
    type: UPDATE_NOTIFY_RECEIVE_CONFIG_COMPLETE,
    payload,
  };
}

export function updateAllNotifyReceiveConfig(payload) {
  return {
    type: UPDATE_ALL_NOTIFY_RECEIVE_CONFIG,
    payload,
  };
}

export function updateAllNotifyReceiveConfigComplete(payload) {
  return {
    type: UPDATE_ALL_NOTIFY_RECEIVE_CONFIG_COMPLETE,
    payload,
  };
}
