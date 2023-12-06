/*
 *
 * StaffList reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_STAFF,
  FETCH_ALL_STAFF_COMPLETE,
  DELETE_STAFF,
  DELETE_STAFF_COMPLETE,
  FETCH_GROUP_AUTH,
  FETCH_GROUP_AUTH_COMPLETE,
  IMPORT_STAFF,
  IMPORT_STAFF_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  totalPage: 1,
  data: [],
  deleting: false,
  authGroup: {
    loading: false,
    lst: [],
  },
  importing: false,
});

function staffListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_STAFF:
      return state.set("loading", true);
    case DELETE_STAFF:
      return state.set("deleting", true);
    case DELETE_STAFF_COMPLETE:
      return state.set("deleting", false);
    case FETCH_ALL_STAFF_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }

      return state
        .set("loading", false)
        .set("deleting", false)
        .set("data", fromJS(data))
        .set("totalPage", totalPage);
    }
    case FETCH_GROUP_AUTH:
      return state.setIn(["authGroup", "loading"], true);
    case FETCH_GROUP_AUTH_COMPLETE:
      return state
        .setIn(["authGroup", "loading"], false)
        .setIn(["authGroup", "lst"], fromJS(action.payload || []));
    case IMPORT_STAFF:
      return state.setIn(["importing", true]);
    case IMPORT_STAFF_COMPLETE:
      return state.setIn(["importing", false]);
    default:
      return state;
  }
}

export default staffListReducer;
