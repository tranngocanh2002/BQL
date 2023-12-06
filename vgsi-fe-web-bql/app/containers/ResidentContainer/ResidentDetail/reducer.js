/*
 *
 * ResidentDetail reducer
 *
 */

import { fromJS } from "immutable";
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

export const initialState = fromJS({
  removing: false,
  apartments: {
    loading: true,
    lst: [],
  },
  detail: {
    loading: false,
    data: undefined,
  },
  buildingArea: {
    loading: true,
    tree: [],
  },
  updating: false,
  addingApartment: false,
  verifyPhoneOtp: {
    loading: false,
    success: false,
  },
  changePhone: {
    loading: false,
    success: false,
  },
});

function residentDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_APARTMENT:
      return state.setIn(["apartments", "loading"], true);
    case REMOVE_APARTMENT:
      return state.set("removing", true);
    case REMOVE_APARTMENT_COMPLETE:
      return state.set("removing", false);
    case FETCH_APARTMENT_COMPLETE:
      return state
        .setIn(["apartments", "loading"], false)
        .setIn(["apartments", "lst"], fromJS(action.payload || []))
        .set("removing", false)
        .set("addingApartment", false);
    case FETCH_DETAIL_RESIDENT:
      return state.setIn(["detail", "loading"], true);
    case FETCH_DETAIL_RESIDENT_COMPLETE:
      return state
        .setIn(["detail", "loading"], false)
        .setIn(["detail", "data"], action.payload ? fromJS(action.payload) : -1)
        .set("updating", false);
    case FETCH_BUILDING_AREA:
      return state.setIn(["buildingArea", "loading"], true);
    case FETCH_BUILDING_AREA_COMPLETE:
      return state
        .setIn(["buildingArea", "loading"], false)
        .setIn(["buildingArea", "tree"], fromJS(action.payload || []));
    case UPDATE_DETAIL:
      return state.set("updating", true);
    case UPDATE_DETAIL_COMPLETE:
      return state.set("updating", false);
    case ADDING_APARTMENT:
      return state.set("addingApartment", true);
    case ADDING_APARTMENT_COMPLETE:
      return state.set("addingApartment", false);
    case VERIFY_PHONE_OTP:
      return state.setIn(["verifyPhoneOtp", "loading"], true);
    case VERIFY_PHONE_OTP_COMPLETE:
      return state
        .setIn(["verifyPhoneOtp", "loading"], false)
        .setIn(["verifyPhoneOtp", "success"], action.payload);
    case CHANGE_PHONE:
      return state.setIn(["changePhone", "loading"], true);
    case CHANGE_PHONE_COMPLETE:
      return state
        .setIn(["changePhone", "loading"], false)
        .setIn(["changePhone", "success"], action.payload);
    default:
      return state;
  }
}

export default residentDetailReducer;
