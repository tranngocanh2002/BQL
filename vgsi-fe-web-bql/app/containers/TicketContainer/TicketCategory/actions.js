/*
 *
 * TicketCategory actions
 *
 */

import { DEFAULT_ACTION, FETCH_AUTH_GROUP, FETCH_AUTH_GROUP_COMPLETE, CREATE_CATEGORY, CREATE_CATEGORY_COMPLETE, FETCH_CATEGORY, FETCH_CATEGORY_COMPLETE, DELETE_CATEGORY, DELETE_CATEGORY_COMPLETE, UPDATE_CATEGORY, UPDATE_CATEGORY_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchAuthGroupAction() {
  return {
    type: FETCH_AUTH_GROUP
  };
}

export function fetchAuthGroupCompleteAction(payload) {
  return {
    type: FETCH_AUTH_GROUP_COMPLETE,
    payload
  };
}

export function createCategoryAction(payload) {
  return {
    type: CREATE_CATEGORY,
    payload
  };
}

export function createCategoryCompleteAction(payload) {
  return {
    type: CREATE_CATEGORY_COMPLETE,
    payload
  };
}

export function fetchCategoryAction(payload) {
  return {
    type: FETCH_CATEGORY,
    payload
  };
}

export function fetchCategoryCompleteAction(payload) {
  return {
    type: FETCH_CATEGORY_COMPLETE,
    payload
  };
}

export function deleteCategoryAction(payload) {
  return {
    type: DELETE_CATEGORY,
    payload
  };
}

export function deleteCategoryCompleteAction(payload) {
  return {
    type: DELETE_CATEGORY_COMPLETE,
    payload
  };
}

export function updateCategoryAction(payload) {
  return {
    type: UPDATE_CATEGORY,
    payload
  };
}

export function updateCategoryCompleteAction(payload) {
  return {
    type: UPDATE_CATEGORY_COMPLETE,
    payload
  };
}
