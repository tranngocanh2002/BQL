/*
 *
 * Roles actions
 *
 */

import { DEFAULT_ACTION, FETCH_ALL_GROUP, FETCH_ALL_GROUP_COMPLETE, DELETE_GROUP_COMPLETE, DELETE_GROUP } from './constants';

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchAllGroup(payload) {
  return {
    type: FETCH_ALL_GROUP,
    payload
  };
}

export function fetchAllGroupComplete(payload) {
  return {
    type: FETCH_ALL_GROUP_COMPLETE,
    payload
  };
}

export function deleteGroup(payload) {
  return {
    type: DELETE_GROUP,
    payload
  };
}

export function deleteGroupComplete(payload) {
  return {
    type: DELETE_GROUP_COMPLETE,
    payload
  };
}
