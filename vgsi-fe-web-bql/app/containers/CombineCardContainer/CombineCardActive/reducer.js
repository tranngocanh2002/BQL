/*
 *
 * CardActive reducer
 *
 */

import { fromJS } from "immutable";
import {
  CREATE_ACTIVE_CARD,
  CREATE_ACTIVE_CARD_COMPLETE,
  DEFAULT_ACTION,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_MEMBER,
  FETCH_MEMBER_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: true,
  data: [],
  apartments: {
    loading: true,
    lst: [],
  },
  members: {
    loading: true,
    lst: [],
  },
  create: {
    loading: false,
    data: {},
    success: false,
  },
});

function cardActiveReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_APARTMENT:
      return state.setIn(["apartments", "loading"], true);
    case FETCH_APARTMENT_COMPLETE:
      return state
        .setIn(["apartments", "loading"], false)
        .setIn(
          ["apartments", "lst"],
          action.payload ? fromJS(action.payload) : -1
        );
    case FETCH_MEMBER:
      return state.setIn(["members", "loading"], true);
    case FETCH_MEMBER_COMPLETE:
      return state
        .setIn(["members", "loading"], false)
        .setIn(["members", "lst"], fromJS(action.payload || []));
    case CREATE_ACTIVE_CARD:
      return state
        .setIn(["create", "loading"], true)
        .setIn(["create", "success"], false);
    case CREATE_ACTIVE_CARD_COMPLETE:
      return state
        .setIn(["create", "loading"], false)
        .setIn(
          ["create", "success"],
          action.payload ? action.payload.status : false
        )
        .setIn(["create", "data"], action.payload ? action.payload.data : {});
    default:
      return state;
  }
}

export default cardActiveReducer;
