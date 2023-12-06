/*
 *
 * RolesCreate actions
 *
 */

import { DEFAULT_ACTION, FETCH_ALL_PERMISSION, FETCH_ALL_PERMISSION_COMPLETE, CREATE_GROUP_AUTH, CREATE_GROUP_AUTH_COMPLETE, FETCH_DETAIL, FETCH_DETAIL_COMPLETE, CREATE_AUTH_ITEM_WEB, CREATE_AUTH_ITEM_WEB_COMPLETE } from './constants';

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}
export function fetchAllPermission() {
  return {
    type: FETCH_ALL_PERMISSION,
  };
}
export function fetchAllPermissionComplete(payload) {
  return {
    type: FETCH_ALL_PERMISSION_COMPLETE,
    payload
  };
}
export function createGroupAuth(payload) {
  return {
    type: CREATE_GROUP_AUTH,
    payload
  };
}
export function createGroupAuthComplete(payload) {
  return {
    type: CREATE_GROUP_AUTH_COMPLETE,
    payload
  };
}
export function fetchGroupAuthDetail(payload) {
  return {
    type: FETCH_DETAIL,
    payload
  };
}
export function fetchGroupAuthDetailComplete(payload) {
  return {
    type: FETCH_DETAIL_COMPLETE,
    payload
  };
}

export function createAuthItemWeb(payload) {
  return {
    type: CREATE_AUTH_ITEM_WEB,
    payload
  };
}

export function createAuthItemWebComplete(payload) {
  return {
    type: CREATE_AUTH_ITEM_WEB_COMPLETE,
    payload
  };
}
