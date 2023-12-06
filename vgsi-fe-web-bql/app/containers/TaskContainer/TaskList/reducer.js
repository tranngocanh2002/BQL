/*
 *
 * TaskList reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_STAFF,
  FETCH_ALL_STAFF_COMPLETE,
  FETCH_ALL_TASK,
  FETCH_ALL_TASK_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  totalCount: 1,
  currentPage: 1,
  pageCount: 1,
  data: [],
  loadingStaff: false,
  staffs: [],
});

function taskListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_TASK:
      return state.set("loading", true);
    case FETCH_ALL_TASK_COMPLETE: {
      let data = [];
      let totalCount = 0;
      let pageCount = 0;

      if (action.payload) {
        data = action.payload.data;
        totalCount = action.payload.totalCount;
        pageCount = action.payload.pageCount;
      }
      return state
        .set("data", fromJS(data))
        .set("pageCount", pageCount)
        .set("totalCount", totalCount)
        .set("loading", false);
    }
    case FETCH_ALL_STAFF:
      return state.set("loadingStaff", true);
    case FETCH_ALL_STAFF_COMPLETE:
      return state
        .set("loadingStaff", false)
        .set("staffs", fromJS(action.payload.data));

    default:
      return state;
  }
}

export default taskListReducer;
