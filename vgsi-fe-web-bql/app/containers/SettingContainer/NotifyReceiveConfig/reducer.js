/*
 *
 * Notify Receive Config reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_NOTIFY_RECEIVE_CONFIG,
  FETCH_ALL_NOTIFY_RECEIVE_CONFIG_COMPLETE,
  UPDATE_NOTIFY_RECEIVE_CONFIG,
  UPDATE_NOTIFY_RECEIVE_CONFIG_COMPLETE,
  UPDATE_ALL_NOTIFY_RECEIVE_CONFIG,
  UPDATE_ALL_NOTIFY_RECEIVE_CONFIG_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  update_all: false,
  receives: {
    loading: false,
    data: [],
  },
});

function receivesReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_NOTIFY_RECEIVE_CONFIG:
      return state.setIn(["receives", "loading"], true);
    case FETCH_ALL_NOTIFY_RECEIVE_CONFIG_COMPLETE:
      return state
        .setIn(["receives", "loading"], false)
        .setIn(["receives", "data"], action.payload);
    case UPDATE_NOTIFY_RECEIVE_CONFIG:
      return state.set("loading", true);
    case UPDATE_NOTIFY_RECEIVE_CONFIG_COMPLETE:
      return state.set("loading", action.payload);
    case UPDATE_ALL_NOTIFY_RECEIVE_CONFIG:
      return state.set("update_all", true);
    case UPDATE_ALL_NOTIFY_RECEIVE_CONFIG_COMPLETE:
      return state.set("update_all", action.payload);
    default:
      return state;
  }
}

export default receivesReducer;
