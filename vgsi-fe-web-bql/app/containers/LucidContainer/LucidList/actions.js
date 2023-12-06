/*
 *
 * LucidList actions
 *
 */

import { DEFAULT_ACTION, FETCH_ALL_LUCID_COMPLETE, FETCH_ALL_LUCID, FETCH_ALL_RESIDENT, FETCH_ALL_RESIDENT_COMPLETE, FETCH_VEHICLE, FETCH_VEHICLE_COMPLETE, FETCH_ALL_APARTMENT, FETCH_ALL_APARTMENT_COMPLETE } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchAllLucid(payload) {
  return {
    type: FETCH_ALL_LUCID,
    payload
  };
}

export function fetchAllLucidComplete(payload) {
  return {
    type: FETCH_ALL_LUCID_COMPLETE,
    payload
  };
}

export function fetchAllResident(payload) {
  return {
    type: FETCH_ALL_RESIDENT,
    payload
  };
}

export function fetchAllResidentComplete(payload) {
  return {
    type: FETCH_ALL_RESIDENT_COMPLETE,
    payload
  };
}

export function fetchVehicle(payload) {
  return {
    type: FETCH_VEHICLE,
    payload
  };
}

export function fetchVehicleComplete(payload) {
  return {
    type: FETCH_VEHICLE_COMPLETE,
    payload
  };
}

export function fetchApartment(payload) {
  return {
    type: FETCH_ALL_APARTMENT,
    payload
  };
}

export function fetchApartmentComplete(payload) {
  return {
    type: FETCH_ALL_APARTMENT_COMPLETE,
    payload
  };
}
