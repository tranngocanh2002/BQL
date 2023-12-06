/*
 *
 * SettingPlane actions
 *
 */

import {
  DEFAULT_ACTION, CREATE_AREA, CREATE_AREA_COMPLETE,
  GET_BUILDING_AREA, GET_BUILDING_AREA_COMPLETE, UPDATE_AREA, UPDATE_AREA_COMPLETE
    , DELETE_AREA
    , DELETE_AREA_COMPLETE
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}
export function createAreaAction(payload) {
  return {
    type: CREATE_AREA,
    payload
  };
}
export function createAreaCompleteAction(payload) {
  return {
    type: CREATE_AREA_COMPLETE,
    payload
  };
}
export function updateAreaAction(payload) {
  return {
    type: UPDATE_AREA,
    payload
  };
}
export function updateAreaCompleteAction(payload) {
  return {
    type: UPDATE_AREA_COMPLETE,
    payload
  };
}
export function deleteAreaAction(payload) {
    return {
        type: DELETE_AREA,
        payload
    };
}
export function deleteAreaCompleteAction(payload) {
    return {
        type: DELETE_AREA_COMPLETE,
        payload
    };
}
export function getBuildingAreaAction(payload) {
  return {
    type: GET_BUILDING_AREA,
    payload
  };
}
export function getBuildingAreaCompleteAction(payload) {
  return {
    type: GET_BUILDING_AREA_COMPLETE,
    payload
  };
}
