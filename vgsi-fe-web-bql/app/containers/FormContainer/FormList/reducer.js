/*
 *
 * FORM reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_FORM,
  FETCH_ALL_FORM_COMPLETE,
  UPDATE_STATUS_FORM,
  UPDATE_STATUS_FORM_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  totalPage: 1,
  updating: false,
  data: [],
});

function formListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_FORM:
      return state.set("loading", true);
    case UPDATE_STATUS_FORM:
      return state.set("updating", true);
    case UPDATE_STATUS_FORM_COMPLETE:
      return state.set("updating", false);
    case FETCH_ALL_FORM_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }
      return state
        .set("loading", false)
        .set("data", fromJS(data))
        .set("totalPage", totalPage);
    }
    default:
      return state;
  }
}

export default formListReducer;
