/*
 *
 * ServiceList reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION_LIST,
  FETCH_ALL_SERVICE_LIST,
  FETCH_ALL_SERVICE_LIST_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  items: [],
});

function serviceListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION_LIST:
      return initialState;
    case FETCH_ALL_SERVICE_LIST:
      return state.set("loading", true);
    case FETCH_ALL_SERVICE_LIST_COMPLETE:
      return state.set("loading", false).set("items", action.payload || []);
    default:
      return state;
  }
}

export default serviceListReducer;
