/*
 *
 * ActionLogSystem actions
 *
 */

import { DEFAULT_ACTION, FETCH_ACTION_CONTROLER, FETCH_USER_MANAGEMENT, FETCH_USER_MANAGEMENT_COMPLETE, FETCH_ACTION_CONTROLER_COMPLETE, FETCH_LOGS, FETCH_LOGS_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchActionController(payload) {
  return {
    type: FETCH_ACTION_CONTROLER,
    payload
  };
}

export function fetchActionControllerComplete(payload) {
  return {
    type: FETCH_ACTION_CONTROLER_COMPLETE,
    payload
  };
}

export function fetchUserManagement(payload) {
  return {
    type: FETCH_USER_MANAGEMENT,
    payload
  };
}

export function fetchUserManagementComplete(payload) {
  return {
    type: FETCH_USER_MANAGEMENT_COMPLETE,
    payload
  };
}

export function fetchLogs(payload) {
  return {
    type: FETCH_LOGS,
    payload
  };
}

export function fetchLogsComplete(payload) {
  return {
    type: FETCH_LOGS_COMPLETE,
    payload
  };
}
