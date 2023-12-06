/*
 *
 * FormDetail reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_DETAIL_FORM,
  FETCH_DETAIL_FORM_COMPLETE,
  UPDATE_DETAIL,
  UPDATE_DETAIL_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  detail: {
    loading: false,
    data: [],
  },
  updating: false,
  success: false,
});

function formDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;

    case FETCH_DETAIL_FORM:
      return state.setIn(["detail", "loading"], true);
    case FETCH_DETAIL_FORM_COMPLETE:
      return state
        .setIn(["detail", "loading"], false)
        .setIn(["detail", "data"], action.payload ? fromJS(action.payload) : -1)
        .set("updating", false);
    case UPDATE_DETAIL:
      return state.set("updating", true).set("success", false);
    case UPDATE_DETAIL_COMPLETE:
      return state
        .set("updating", false)
        .set("success", action.payload || false);
    default:
      return state;
  }
}

export default formDetailReducer;
