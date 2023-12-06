/*
 *
 * ApartmentDetail reducer
 *
 */

import { fromJS } from "immutable";
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

export const initialState = fromJS({
  members: {
    loading: true,
    lst: [],
  },
  removing: false,
  detail: {
    loading: false,
    data: undefined,
  },
  buildingArea: {
    tree: [],
  },
  allResident: {
    loading: false,
    totalPage: 1,
    data: [],
  },
  updating: false,
  addingMember: false,
  updatingMember: false,
  success: false,
  apartment_type: {
    loading: false,
    data: [],
  },
});

function apartmentDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_MEMBER:
      return state.setIn(["members", "loading"], true);
    case REMOVE_MEMBER:
      return state.set("removing", true);
    case REMOVE_MEMBER_COMPLETE:
      return state.set("removing", false);
    case FETCH_MEMBER_COMPLETE:
      return state
        .setIn(["members", "loading"], false)
        .setIn(["members", "lst"], fromJS(action.payload || []))
        .set("removing", false)
        .set("addingMember", false);
    case FETCH_DETAIL_APARTMENT:
      return state.setIn(["detail", "loading"], true);
    case FETCH_DETAIL_APARTMENT_COMPLETE:
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
      return state.set("updating", true).set("success", false);
    case UPDATE_DETAIL_COMPLETE:
      return state
        .set("updating", false)
        .set("success", action.payload || false);
    case ADDING_MEMBER:
      return state.set("addingMember", true).set("success", false);
    case ADDING_MEMBER_COMPLETE:
      return state
        .set("addingMember", false)
        .set("success", action.payload || false);
    case UPDATING_MEMBER:
      return state.set("updatingMember", true).set("success", false);
    case UPDATING_MEMBER_COMPLETE:
      return state
        .set("updatingMember", false)
        .set("success", action.payload || false);
    case FETCH_ALL_APARTMENT_TYPE: {
      return state.setIn(["apartment_type", "loading"], true);
    }
    case FETCH_ALL_APARTMENT_TYPE_COMPLETE: {
      return state
        .setIn(["apartment_type", "loading"], false)
        .setIn(["apartment_type", "data"], fromJS(action.payload || []));
    }
    case FETCH_ALL_RESIDENT_BY_PHONE:
      return state.setIn(["allResident", "loading"], true);
    case FETCH_ALL_RESIDENT_BY_PHONE_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (!!action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }
      return state
        .setIn(["allResident", "loading"], false)
        .setIn(["allResident", "data"], fromJS(data))
        .setIn(["allResident", "totalPage"], totalPage);
    }
    default:
      return state;
  }
}

export default apartmentDetailReducer;
