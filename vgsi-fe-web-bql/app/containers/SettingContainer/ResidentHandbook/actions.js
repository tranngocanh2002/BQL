/*
 *
 * ResidentHandbook actions
 *
 */

import { DEFAULT_ACTION, FETCH_CATEGORY, FETCH_CATEGORY_COMPLETE, ADD_CATEGORY, ADD_CATEGORY_COMPLETE, EDIT_CATEGORY, EDIT_CATEGORY_COMPLETE, DELETE_CATEGORY, DELETE_CATEGORY_COMPLETE, FETCH_HANDBOOK_ITEM, FETCH_HANDBOOK_ITEM_COMPLETE, ADD_HANDBOOK_ITEM, ADD_HANDBOOK_ITEM_COMPLETE, EDIT_HANDBOOK_ITEM, EDIT_HANDBOOK_ITEM_COMPLETE, DELETE_HANDBOOK_ITEM, DELETE_HANDBOOK_ITEM_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchCategory(payload) {
  return {
    type: FETCH_CATEGORY,
    payload
  };
}

export function fetchCategoryComplete(payload) {
  return {
    type: FETCH_CATEGORY_COMPLETE,
    payload
  };
}

export function addCategory(payload) {
  return {
    type: ADD_CATEGORY,
    payload
  };
}

export function addCategoryComplete(payload) {
  return {
    type: ADD_CATEGORY_COMPLETE,
    payload
  };
}

export function editCategory(payload) {
  return {
    type: EDIT_CATEGORY,
    payload
  };
}

export function editCategoryComplete(payload) {
  return {
    type: EDIT_CATEGORY_COMPLETE,
    payload
  };
}

export function deleteCategory(payload) {
  return {
    type: DELETE_CATEGORY,
    payload
  };
}

export function deleteCategoryComplete(payload) {
  return {
    type: DELETE_CATEGORY_COMPLETE,
    payload
  };
}

export function fetchHandbook(payload) {
  return {
    type: FETCH_HANDBOOK_ITEM,
    payload
  };
}

export function fetchHandbookComplete(payload) {
  return {
    type: FETCH_HANDBOOK_ITEM_COMPLETE,
    payload
  };
}

export function addHandbook(payload) {
  return {
    type: ADD_HANDBOOK_ITEM,
    payload
  };
}

export function addHandbookComplete(payload) {
  return {
    type: ADD_HANDBOOK_ITEM_COMPLETE,
    payload
  };
}

export function editHandbook(payload) {
  return {
    type: EDIT_HANDBOOK_ITEM,
    payload
  };
}

export function editHandbookComplete(payload) {
  return {
    type: EDIT_HANDBOOK_ITEM_COMPLETE,
    payload
  };
}

export function deleteHandbook(payload) {
  return {
    type: DELETE_HANDBOOK_ITEM,
    payload
  };
}

export function deleteHandbookComplete(payload) {
  return {
    type: DELETE_HANDBOOK_ITEM_COMPLETE,
    payload
  };
}
