/*
 *
 * VihicleManagement actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_ALL_VEHICLE,
  FETCH_ALL_VEHICLE_COMPLETE,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_ALL_FEE_LEVEL,
  FETCH_ALL_FEE_LEVEL_COMPLETE,
  CREATE_VEHICLE,
  CREATE_VEHICLE_COMPLETE,
  UPDATE_VEHICLE,
  UPDATE_VEHICLE_COMPLETE,
  DELETE_VEHICLE,
  DELETE_VEHICLE_COMPLETE,
  IMPORT_VEHICLE,
  IMPORT_VEHICLE_COMPLETE,
  ACTIVE_VEHICLE,
  ACTIVE_VEHICLE_COMPLETE,
  CANCEL_VEHICLE,
  CANCEL_VEHICLE_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchApartment(payload) {
  return {
    type: FETCH_APARTMENT,
    payload,
  };
}

export function fetchApartmentComplete(payload) {
  return {
    type: FETCH_APARTMENT_COMPLETE,
    payload,
  };
}

export function fetchAllVehicle(payload) {
  return {
    type: FETCH_ALL_VEHICLE,
    payload,
  };
}

export function fetchAllVehicleComplete(payload) {
  return {
    type: FETCH_ALL_VEHICLE_COMPLETE,
    payload,
  };
}

export function fetchAllFeeLevel(payload) {
  return {
    type: FETCH_ALL_FEE_LEVEL,
    payload,
  };
}

export function fetchAllFeeLevelComplete(payload) {
  return {
    type: FETCH_ALL_FEE_LEVEL_COMPLETE,
    payload,
  };
}

export function createVehicle(payload) {
  return {
    type: CREATE_VEHICLE,
    payload,
  };
}

export function createVehicleComplete(payload) {
  return {
    type: CREATE_VEHICLE_COMPLETE,
    payload,
  };
}

export function updateVehicle(payload) {
  return {
    type: UPDATE_VEHICLE,
    payload,
  };
}

export function updateVehicleComplete(payload) {
  return {
    type: UPDATE_VEHICLE_COMPLETE,
    payload,
  };
}

export function deleteVehicle(payload) {
  return {
    type: DELETE_VEHICLE,
    payload,
  };
}

export function deleteVehicleComplete(payload) {
  return {
    type: DELETE_VEHICLE_COMPLETE,
    payload,
  };
}

export function importVehicle(payload) {
  return {
    type: IMPORT_VEHICLE,
    payload,
  };
}

export function importVehicleComplete(payload) {
  return {
    type: IMPORT_VEHICLE_COMPLETE,
    payload,
  };
}

export function activeVehicle(payload) {
  return {
    type: ACTIVE_VEHICLE,
    payload,
  };
}

export function activeVehicleComplete(payload) {
  return {
    type: ACTIVE_VEHICLE_COMPLETE,
    payload,
  };
}

export function cancelVehicle(payload) {
  return {
    type: CANCEL_VEHICLE,
    payload,
  };
}

export function cancelVehicleComplete(payload) {
  return {
    type: CANCEL_VEHICLE_COMPLETE,
    payload,
  };
}
