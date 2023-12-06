/*
 *
 * TicketDetail actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_TICKET_DETAIL,
  FETCH_TICKET_DETAIL_COMPLETE,
  FETCH_EXTERNAL_MESSAGES,
  FETCH_EXTERNAL_MESSAGES_COMPLETE,
  FETCH_INTERNAL_MESSAGES,
  FETCH_INTERNAL_MESSAGES_COMPLETE,
  UPDATE_TICKET_STATUS,
  UPDATE_TICKET_STATUS_COMPLETE,
  FETCH_MANAGERMENT_GROUPS,
  ADD_MANAGERMENT_GROUPS,
  ADD_MANAGERMENT_GROUPS_COMPLETE,
  FETCH_MANAGERMENT_GROUPS_COMPLETE,
  SEND_EXTERNAL_MESSAGE,
  SEND_EXTERNAL_MESSAGE_COMPLETE,
  SEND_INTERNAL_MESSAGE,
  SEND_INTERNAL_MESSAGE_COMPLETE,
  FETCH_AUTH_GROUP,
  FETCH_AUTH_GROUP_COMPLETE,
  REMOVE_MANAGERMENT_GROUPS,
  REMOVE_MANAGERMENT_GROUPS_COMPLETE,
  FETCH_CATEGORY,
  FETCH_CATEGORY_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchTicketDetailAction(payload) {
  return {
    type: FETCH_TICKET_DETAIL,
    payload,
  };
}
export function fetchTicketDetailCompleteAction(payload) {
  return {
    type: FETCH_TICKET_DETAIL_COMPLETE,
    payload,
  };
}

export function fetchExternalMessagesAction(payload) {
  return {
    type: FETCH_EXTERNAL_MESSAGES,
    payload,
  };
}
export function fetchExternalMessagesCompleteAction(payload) {
  return {
    type: FETCH_EXTERNAL_MESSAGES_COMPLETE,
    payload,
  };
}

export function fetchInternalMessagesAction(payload) {
  return {
    type: FETCH_INTERNAL_MESSAGES,
    payload,
  };
}
export function fetchInternalMessagesCompleteAction(payload) {
  return {
    type: FETCH_INTERNAL_MESSAGES_COMPLETE,
    payload,
  };
}

export function updateTicketStatusAction(payload) {
  return {
    type: UPDATE_TICKET_STATUS,
    payload,
  };
}
export function updateTicketStatusCompleteAction(payload) {
  return {
    type: UPDATE_TICKET_STATUS_COMPLETE,
    payload,
  };
}

export function fetchManagerGroupsAction(payload) {
  return {
    type: FETCH_MANAGERMENT_GROUPS,
    payload,
  };
}
export function addManagerGroupsAction(payload) {
  return {
    type: ADD_MANAGERMENT_GROUPS,
    payload,
  };
}

export function addManagerGroupsCompleteAction(payload) {
  return {
    type: ADD_MANAGERMENT_GROUPS_COMPLETE,
    payload,
  };
}

export function removeManagerGroupsAction(payload) {
  return {
    type: REMOVE_MANAGERMENT_GROUPS,
    payload,
  };
}

export function removeManagerGroupsCompleteAction(payload) {
  return {
    type: REMOVE_MANAGERMENT_GROUPS_COMPLETE,
    payload,
  };
}

export function fetchManagerGroupsCompleteAction(payload) {
  return {
    type: FETCH_MANAGERMENT_GROUPS_COMPLETE,
    payload,
  };
}

export function sendExternalMessageAction(payload) {
  return {
    type: SEND_EXTERNAL_MESSAGE,
    payload,
  };
}
export function sendExternalMessageCompleteAction(payload) {
  return {
    type: SEND_EXTERNAL_MESSAGE_COMPLETE,
    payload,
  };
}

export function sendInternalMessageAction(payload) {
  return {
    type: SEND_INTERNAL_MESSAGE,
    payload,
  };
}
export function sendInternalMessageCompleteAction(payload) {
  return {
    type: SEND_INTERNAL_MESSAGE_COMPLETE,
    payload,
  };
}

export function fetchAuthGroupAction() {
  return {
    type: FETCH_AUTH_GROUP,
  };
}

export function fetchAuthGroupCompleteAction(payload) {
  return {
    type: FETCH_AUTH_GROUP_COMPLETE,
    payload,
  };
}
export function fetchCategoryAction(payload) {
  return {
    type: FETCH_CATEGORY,
    payload,
  };
}

export function fetchCategoryCompleteAction(payload) {
  return {
    type: FETCH_CATEGORY_COMPLETE,
    payload,
  };
}
