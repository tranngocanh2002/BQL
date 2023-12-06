/*
 *
 * ResidentAdd reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  CREATE_RESIDENT,
  CREATE_RESIDENT_COMPLETE,
  UPDATE_RESIDENT,
  UPDATE_RESIDENT_COMPLETE,
  FETCH_DETAIL,
  FETCH_DETAIL_COMPLETE,
  FTECH_APARTMENT,
  FTECH_APARTMENT_COMPLETE,
  FETCH_ALL_RESIDENT_BY_PHONE,
  FETCH_ALL_RESIDENT_BY_PHONE_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  apartments: {
    loading: false,
    lst: [],
  },
  creating: false,
  success: false,
  updating: false,
  updateSuccess: false,
  detail: {
    loading: false,
    data: undefined,
  },
  allResident: {
    loading: false,
    totalPage: 1,
    data: [],
  },
});

function residentAddReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case CREATE_RESIDENT:
      return state.set("creating", true);
    case CREATE_RESIDENT_COMPLETE:
      return state
        .set("creating", false)
        .set("success", action.payload || false);
    case UPDATE_RESIDENT:
      return state.set("updating", true);
    case UPDATE_RESIDENT_COMPLETE:
      return state
        .set("updating", false)
        .set("updateSuccess", action.payload || false);
    case FETCH_DETAIL:
      return state.setIn(["detail", "loading"], true);
    case FETCH_DETAIL_COMPLETE:
      return state
        .setIn(["detail", "loading"], false)
        .setIn(
          ["detail", "data"],
          action.payload ? fromJS(action.payload) : -1
        );
    case FTECH_APARTMENT:
      return state.setIn(["apartments", "loading"], true);
    case FTECH_APARTMENT_COMPLETE:
      return state
        .setIn(["apartments", "loading"], false)
        .setIn(
          ["apartments", "lst"],
          action.payload ? fromJS(action.payload) : -1
        );
    case FETCH_ALL_RESIDENT_BY_PHONE:
      return state.setIn(["allResident", "loading"], true);
    case FETCH_ALL_RESIDENT_BY_PHONE_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (action.payload) {
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

export default residentAddReducer;
