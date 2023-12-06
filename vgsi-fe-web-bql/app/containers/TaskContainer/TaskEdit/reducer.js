/*
 *
 * TaskEdit reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_STAFF,
  FETCH_ALL_STAFF_COMPLETE,
  FETCH_DETAIL_TASK,
  FETCH_DETAIL_TASK_COMPLETE,
  UPDATE_TASK,
  UPDATE_TASK_COMPLETE
} from "./constants";

export const initialState = fromJS({
  loadingStaff: false,
  totalPage: 1,
  staff: [],
  task: {},
  loading: false,
  success: false,
});

function taskEditReducer(state = initialState, action) {
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
    case FETCH_DETAIL_TASK:
      return state.set("loading", true);
    case FETCH_DETAIL_TASK_COMPLETE:
      return state.set("loading", false).set("task", {
        ...action.payload,
        people_involved:
          action.payload.people_involveds.map(
            (peo) => peo.id
          ),
        performer: action.payload.performers.map(
          (per) => per.id
        ),
      });
      case UPDATE_TASK:
        return state.set("loading", true).set("success", false);
      case UPDATE_TASK_COMPLETE:
        return state.set("loading", false).set("success", action.payload);
    default:
      return state;
  }
}

export default taskEditReducer;
