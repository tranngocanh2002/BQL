/*
 *
 * NotificationFeeManager actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  CREATE_ANNOUNCEMENT_TEMPLATE_FEE,
  CREATE_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  UPDATE_ANNOUNCEMENT_TEMPLATE_FEE,
  UPDATE_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  SHOW_CHOOSE_TEMPLATE_LIST,
  CHOOSE_CREATE_TEMPLATE,
  DELETE_ANNOUNCEMENT_TEMPLATE_FEE,
  DELETE_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
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

export function createAnnouncementFeeTemplate(payload) {
  return {
    type: CREATE_ANNOUNCEMENT_TEMPLATE_FEE,
    payload,
  };
}

export function createAnnouncementFeeTemplateComplete(payload) {
  return {
    type: CREATE_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
    payload,
  };
}

export function updateAnnouncementFeeTemplate(payload) {
  return {
    type: UPDATE_ANNOUNCEMENT_TEMPLATE_FEE,
    payload,
  };
}

export function updateAnnouncementFeeTemplateComplete(payload) {
  return {
    type: UPDATE_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
    payload,
  };
}

export function showChooseTemplateList(payload) {
  return {
    type: SHOW_CHOOSE_TEMPLATE_LIST,
    payload,
  };
}
export function chooseCreateTemplate(payload) {
  return {
    type: CHOOSE_CREATE_TEMPLATE,
    payload,
  };
}

export function deleteAnnouncementFeeTemplate(payload) {
  return {
    type: DELETE_ANNOUNCEMENT_TEMPLATE_FEE,
    payload,
  };
}

export function deleteAnnouncementFeeTemplateComplete(payload) {
  return {
    type: DELETE_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
    payload,
  };
}
