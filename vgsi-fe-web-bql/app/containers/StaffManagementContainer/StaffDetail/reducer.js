/*
 *
 * StaffDetail reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_DETAIL_STAFF,
  FETCH_DETAIL_STAFF_COMPLETE,
  RESET_PASSWORD_STAFF,
  RESET_PASSWORD_STAFF_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  detail: {
    loading: false,
    data: undefined,
  },
  resetPassword: {
    changing: false,
    success: false,
  },
});

function staffDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_DETAIL_STAFF:
      return state.setIn(["detail", "loading"], true);
    case FETCH_DETAIL_STAFF_COMPLETE:
      return state
        .setIn(["detail", "loading"], false)
        .setIn(
          ["detail", "data"],
          action.payload ? fromJS(action.payload) : -1
        );
    case RESET_PASSWORD_STAFF:
      return state
        .setIn(["resetPassword", "changing"], true)
        .setIn(["resetPassword", "success"], false);
    case RESET_PASSWORD_STAFF_COMPLETE:
      return state
        .setIn(["resetPassword", "changing"], false)
        .setIn(["resetPassword", "success"], action.payload || false);
    default:
      return state;
  }
}

export default staffDetailReducer;
