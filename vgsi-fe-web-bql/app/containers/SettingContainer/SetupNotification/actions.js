/*
 *
 * Roles actions
 *
 */

import { DEFAULT_ACTION, FETCH_ALL_GROUP, FETCH_ALL_GROUP_COMPLETE, DELETE_GROUP_COMPLETE, DELETE_GROUP, FETCH_BUILDING_CLUSTER, FETCH_BUILDING_CLUSTER_COMPLETE, FETCH_ALL_ROLES, FETCH_ALL_ROLES_COMPLETE, UPDATE_SETTING, UPDATE_SETTING_COMPLETE, DELETE_ROW, DELETE_ROW_COMPLETE } from './constants';

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchBuildingCluster(payload) {
  return {
    type: FETCH_BUILDING_CLUSTER,
    payload
  };
}

export function fetchBuildingClusterComplete(payload) {
  return {
    type: FETCH_BUILDING_CLUSTER_COMPLETE,
    payload
  };
}

export function fetchAllRoles(payload) {
  return {
    type: FETCH_ALL_ROLES,
    payload
  };
}

export function fetchAllRolesComplete(payload) {
  return {
    type: FETCH_ALL_ROLES_COMPLETE,
    payload
  };
}
export function updateSetting(payload) {
  return {
    type: UPDATE_SETTING,
    payload
  };
}

export function updateSettingComplete(payload) {
  return {
    type: UPDATE_SETTING_COMPLETE,
    payload
  };
}
export function deleteSetting(payload) {
  return {
    type: DELETE_ROW,
    payload
  };
}

export function deleteSettingComplete(payload) {
  return {
    type: DELETE_ROW_COMPLETE,
    payload
  };
}