/*
 *
 * ResidentDetail actions
 *
 */

import {
  CHANGE_PHONE,
  CHANGE_PHONE_COMPLETE,
  DEFAULT_ACTION,
  VERIFY_PHONE_OTP,
  VERIFY_PHONE_OTP_COMPLETE,
} from "./constants";
import {
  ADDING_APARTMENT,
  ADDING_APARTMENT_COMPLETE,
  FETCH_BUILDING_AREA,
  FETCH_BUILDING_AREA_COMPLETE,
  FETCH_DETAIL_RESIDENT,
  FETCH_DETAIL_RESIDENT_COMPLETE,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  REMOVE_APARTMENT,
  REMOVE_APARTMENT_COMPLETE,
  UPDATE_DETAIL,
  UPDATE_DETAIL_COMPLETE,
} from "../../ResidentContainer/ResidentDetail/constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchApartmentsAction(payload) {
  return {
    type: FETCH_APARTMENT,
    payload,
  };
}

export function fetchApartmentCompleteAction(payload) {
  return {
    type: FETCH_APARTMENT_COMPLETE,
    payload,
  };
}

export function removeApartmentAction(payload) {
  return {
    type: REMOVE_APARTMENT,
    payload,
  };
}

export function removeApartmentCompleteAction(payload) {
  return {
    type: REMOVE_APARTMENT_COMPLETE,
    payload,
  };
}

export function fetchDetailResidentAction(payload) {
  return {
    type: FETCH_DETAIL_RESIDENT,
    payload,
  };
}

export function fetchDetailResidentCompleteAction(payload) {
  return {
    type: FETCH_DETAIL_RESIDENT_COMPLETE,
    payload,
  };
}

export function fetchBuildingAreaAction() {
  return {
    type: FETCH_BUILDING_AREA,
  };
}
export function fetchBuildingAreaCompleteAction(payload) {
  return {
    type: FETCH_BUILDING_AREA_COMPLETE,
    payload,
  };
}

export function updateDetailAction(payload) {
  return {
    type: UPDATE_DETAIL,
    payload,
  };
}
export function updateDetailCompleteAction(payload) {
  return {
    type: UPDATE_DETAIL_COMPLETE,
    payload,
  };
}

export function addApartmentAction(payload) {
  return {
    type: ADDING_APARTMENT,
    payload,
  };
}
export function addApartmentCompleteAction(payload) {
  return {
    type: ADDING_APARTMENT_COMPLETE,
    payload,
  };
}

export function changePhoneAction(payload) {
  return {
    type: CHANGE_PHONE,
    payload,
  };
}

export function changePhoneCompleteAction(payload) {
  return {
    type: CHANGE_PHONE_COMPLETE,
    payload,
  };
}

export function verifyPhoneOtpAction(payload) {
  return {
    type: VERIFY_PHONE_OTP,
    payload,
  };
}

export function verifyPhoneOtpCompleteAction(payload) {
  return {
    type: VERIFY_PHONE_OTP_COMPLETE,
    payload,
  };
}
