/*
 *
 * TaskAdd reducer
 *
 */

import { fromJS } from "immutable";
import {
  CREATE_TASK,
  CREATE_TASK_COMPLETE,
  DEFAULT_ACTION,
  FETCH_ALL_STAFF,
  FETCH_ALL_STAFF_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loadingStaff: false,
  totalPage: 1,
  staff: [],
  loading: false,
  success: false,
});

function taskAddReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_STAFF:
      return state.set("loadingStaff", true);
    case FETCH_ALL_STAFF_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }

      return state
        .set("loadingStaff", false)
        .set("staff", fromJS(data))
        .set("totalPage", totalPage);
    }
    case CREATE_TASK:
      return state.set("loading", true).set("success", action.payload);
    case CREATE_TASK_COMPLETE:
      return state.set("loading", false).set("success", action.payload);
    default:
      return state;
  }
}

export default taskAddReducer;
