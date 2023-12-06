/*
 *
 * AddUltilityPage reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  ADD_ULTILITY_ITEM,
  ADD_ULTILITY_ITEM_COMPLETE,
  UPDATE_ULTILITY_ITEM,
  UPDATE_ULTILITY_ITEM_COMPLETE,
  FETCH_DETAIL,
  FETCH_DETAIL_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  createSuccess: false,
  detail: {
    loading: false,
    data: undefined,
  },
});

function addUltilityPageReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case ADD_ULTILITY_ITEM:
    case UPDATE_ULTILITY_ITEM:
      return state.set("loading", true).set("createSuccess", false);
    case ADD_ULTILITY_ITEM_COMPLETE:
    case UPDATE_ULTILITY_ITEM_COMPLETE:
      return state
        .set("loading", false)
        .set("createSuccess", action.payload || false);
    case FETCH_DETAIL: {
      return state.setIn(["detail", "loading"], true);
    }
    case FETCH_DETAIL_COMPLETE: {
      return state
        .setIn(["detail", "loading"], false)
        .setIn(["detail", "data"], action.payload);
    }
    default:
      return state;
  }
}

export default addUltilityPageReducer;
