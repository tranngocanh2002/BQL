/*
 *
 * StaffAdd reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_GROUP_AUTH,
  FETCH_GROUP_AUTH_COMPLETE,
  CREATE_STAFF,
  CREATE_STAFF_COMPLETE,
  UPDATE_STAFF,
  UPDATE_STAFF_COMPLETE,
  FETCH_DETAIL,
  FETCH_DETAIL_COMPLETE,
  UPDATE_STAFF_AND_USERDETAIL,
  UPDATE_STAFF_AND_USERDETAIL_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  authGroup: {
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
});

function staffAddReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case CREATE_STAFF:
      return state.set("creating", true);
    case CREATE_STAFF_COMPLETE:
      return state
        .set("creating", false)
        .set("success", action.payload || false);
    case UPDATE_STAFF:
      return state.set("updating", true);
    case UPDATE_STAFF_COMPLETE:
      return state
        .set("updating", false)
        .set("updateSuccess", action.payload || false);
    case UPDATE_STAFF_AND_USERDETAIL:
      return state.set("updating", true);
    case UPDATE_STAFF_AND_USERDETAIL_COMPLETE:
      return state
        .set("updating", false)
        .set("updateSuccess", !!action.payload || false);
    case FETCH_GROUP_AUTH:
      return state.setIn(["authGroup", "loading"], true);
    case FETCH_GROUP_AUTH_COMPLETE:
      return state
        .setIn(["authGroup", "loading"], false)
        .setIn(["authGroup", "lst"], fromJS(action.payload || []));
    case FETCH_DETAIL:
      return state.setIn(["detail", "loading"], true);
    case FETCH_DETAIL_COMPLETE:
      return state
        .setIn(["detail", "loading"], false)
        .setIn(
          ["detail", "data"],
          action.payload ? fromJS(action.payload) : -1
        );
    default:
      return state;
  }
}

export default staffAddReducer;
