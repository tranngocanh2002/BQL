/*
 *
 * MaintainList reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_MAINTAIN_DEVICES,
  DELETE_MAINTAIN_DEVICES,
  DELETE_MAINTAIN_DEVICES_COMPLETE,
  FETCH_ALL_MAINTAIN_DEVICES_COMPLETE,
  UPDATE_DETAIL,
  UPDATE_DETAIL_COMPLETE,
  FETCH_ALL_MAINTAIN_SCHEDULE,
  FETCH_ALL_MAINTAIN_SCHEDULE_COMPLETE,
  UPDATE_MAINTAIN_SCHEDULE,
  UPDATE_MAINTAIN_SCHEDULE_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  totalPage: 1,
  data: [],
  loading2: false,
  totalPage2: 1,
  data2: [],
  updating: false,
  updating2: false,
  deleting: false,
  importing: false,
});

function maintainListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_MAINTAIN_DEVICES:
      return state.set("loading", true);
    case FETCH_ALL_MAINTAIN_SCHEDULE:
      return state.set("loading2", true);
    case UPDATE_DETAIL:
      return state.set("updating", true);
    case UPDATE_DETAIL_COMPLETE:
      return state.set("updating", false);
    case DELETE_MAINTAIN_DEVICES:
      return state.set("deleting", true);
    case DELETE_MAINTAIN_DEVICES_COMPLETE:
      return state.set("deleting", false);
    case FETCH_ALL_MAINTAIN_DEVICES_COMPLETE: {
      let data = [];
      let totalPage = 1;
      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalCount;
      }
      return state
        .set("loading", false)
        .set("deleting", false)
        .set("data", fromJS(data))
        .set("totalPage", totalPage);
    }
    case FETCH_ALL_MAINTAIN_SCHEDULE_COMPLETE: {
      let data2 = [];
      let totalPage2 = 1;
      if (action.payload) {
        data2 = action.payload.data;
        totalPage2 = action.payload.totalCount;
      }
      return state
        .set("loading2", false)
        .set("deleting", false)
        .set("data2", fromJS(data2))
        .set("totalPage2", totalPage2);
    }
    case UPDATE_MAINTAIN_SCHEDULE:
      return state.set("updating2", true);
    case UPDATE_MAINTAIN_SCHEDULE_COMPLETE:
      return state.set("updating2", false);
    default:
      return state;
  }
}

export default maintainListReducer;
