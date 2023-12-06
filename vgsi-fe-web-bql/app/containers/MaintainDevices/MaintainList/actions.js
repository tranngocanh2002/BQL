/*
 *
 * MaintainDevicesList actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ALL_MAINTAIN_DEVICES,
  FETCH_ALL_MAINTAIN_DEVICES_COMPLETE,
  DELETE_MAINTAIN_DEVICES,
  DELETE_MAINTAIN_DEVICES_COMPLETE,
  UPDATE_DETAIL,
  UPDATE_DETAIL_COMPLETE,
  FETCH_ALL_MAINTAIN_SCHEDULE,
  FETCH_ALL_MAINTAIN_SCHEDULE_COMPLETE,
  UPDATE_MAINTAIN_SCHEDULE,
  UPDATE_MAINTAIN_SCHEDULE_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}
export function fetchAllMaintainDevicesAction(payload) {
  return {
    type: FETCH_ALL_MAINTAIN_DEVICES,
    payload,
  };
}
export function fetchAllMaintainDevicesCompleteAction(payload) {
  return {
    type: FETCH_ALL_MAINTAIN_DEVICES_COMPLETE,
    payload,
  };
}
export function deleteMaintainDevicesAction(payload) {
  return {
    type: DELETE_MAINTAIN_DEVICES,
    payload,
  };
}
export function deleteMaintainDevicesCompleteAction(payload) {
  return {
    type: DELETE_MAINTAIN_DEVICES_COMPLETE,
    payload,
  };
}

export function updateMaintainDevicesAction(payload) {
  return {
    type: UPDATE_DETAIL,
    payload,
  };
}

export function updateMaintainDevicesCompleteAction(payload) {
  return {
    type: UPDATE_DETAIL_COMPLETE,
    payload,
  };
}
export function fetchAllMaintainScheduleAction(payload) {
  return {
    type: FETCH_ALL_MAINTAIN_SCHEDULE,
    payload,
  };
}

export function fetchAllMaintainScheduleCompleteAction(payload) {
  return {
    type: FETCH_ALL_MAINTAIN_SCHEDULE_COMPLETE,
    payload,
  };
}
export function updateMaintainDevicesScheduleAction(payload) {
  return {
    type: UPDATE_MAINTAIN_SCHEDULE,
    payload,
  };
}

export function updateMaintainDevicesScheduleCompleteAction(payload) {
  return {
    type: UPDATE_MAINTAIN_SCHEDULE_COMPLETE,
    payload,
  };
}
