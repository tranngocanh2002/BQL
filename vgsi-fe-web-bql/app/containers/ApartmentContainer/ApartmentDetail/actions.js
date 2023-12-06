/*
 *
 * ApartmentDetail actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_MEMBER,
  FETCH_MEMBER_COMPLETE,
  REMOVE_MEMBER,
  REMOVE_MEMBER_COMPLETE,
  FETCH_DETAIL_APARTMENT,
  FETCH_DETAIL_APARTMENT_COMPLETE,
  FETCH_BUILDING_AREA,
  FETCH_BUILDING_AREA_COMPLETE,
  UPDATE_DETAIL,
  UPDATE_DETAIL_COMPLETE,
  ADDING_MEMBER,
  ADDING_MEMBER_COMPLETE,
  UPDATING_MEMBER,
  UPDATING_MEMBER_COMPLETE,
  FETCH_ALL_APARTMENT_TYPE,
  FETCH_ALL_APARTMENT_TYPE_COMPLETE,
  FETCH_ALL_RESIDENT_BY_PHONE,
  FETCH_ALL_RESIDENT_BY_PHONE_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchMemberAction(payload) {
  return {
    type: FETCH_MEMBER,
    payload,
  };
}

export function fetchMemberCompleteAction(payload) {
  return {
    type: FETCH_MEMBER_COMPLETE,
    payload,
  };
}

export function removeMemberAction(payload) {
  return {
    type: REMOVE_MEMBER,
    payload,
  };
}

export function removeMemberCompleteAction(payload) {
  return {
    type: REMOVE_MEMBER_COMPLETE,
    payload,
  };
}

export function fetchDetailApartmentAction(payload) {
  return {
    type: FETCH_DETAIL_APARTMENT,
    payload,
  };
}

export function fetchDetailApartmentCompleteAction(payload) {
  return {
    type: FETCH_DETAIL_APARTMENT_COMPLETE,
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

export function addMemberAction(payload) {
  return {
    type: ADDING_MEMBER,
    payload,
  };
}
export function addMemberCompleteAction(payload) {
  return {
    type: ADDING_MEMBER_COMPLETE,
    payload,
  };
}

export function updateMemberAction(payload) {
  return {
    type: UPDATING_MEMBER,
    payload,
  };
}
export function updateMemberCompleteAction(payload) {
  return {
    type: UPDATING_MEMBER_COMPLETE,
    payload,
  };
}

export function fetchAllApartmentType(payload) {
  return {
    type: FETCH_ALL_APARTMENT_TYPE,
    payload,
  };
}
export function fetchAllApartmentTypeComplete(payload) {
  return {
    type: FETCH_ALL_APARTMENT_TYPE_COMPLETE,
    payload,
  };
}

export function fetchAllResidentByPhoneAction(payload) {
  return {
    type: FETCH_ALL_RESIDENT_BY_PHONE,
    payload,
  };
}
export function fetchAllResidentByPhoneCompleteAction(payload) {
  return {
    type: FETCH_ALL_RESIDENT_BY_PHONE_COMPLETE,
    payload,
  };
}
