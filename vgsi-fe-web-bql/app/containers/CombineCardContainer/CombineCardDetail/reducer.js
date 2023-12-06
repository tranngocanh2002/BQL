/*
 *
 * ApartmentDetail reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_DETAIL_COMBINE_CARD,
  FETCH_DETAIL_COMBINE_CARD_COMPLETE,
  DELETE_COMBINE_CARD,
  DELETE_COMBINE_CARD_COMPLETE,
  UPDATE_DETAIL,
  UPDATE_DETAIL_COMPLETE,
  CHANGE_COMBINE_CARD_STATUS_COMPLETE,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_MEMBER,
  FETCH_MEMBER_COMPLETE,
  CREATE_ACTIVE_CARD,
  CREATE_ACTIVE_CARD_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: true,
  deleting: false,
  detail: {
    loading: false,
    data: undefined,
  },
  updating: false,
  success: false,
  apartments: {
    loading: true,
    lst: [],
  },
  members: {
    loading: true,
    lst2: [],
  },
  create: {
    loading: false,
    data: {},
    success: false,
  },
});

function combineCardDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_DETAIL_COMBINE_CARD:
      return state.setIn(["detail", "loading"], true);
    case FETCH_DETAIL_COMBINE_CARD_COMPLETE:
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
    case FETCH_APARTMENT:
      return state.setIn(["apartments", "loading"], true);
    case FETCH_APARTMENT_COMPLETE:
      return state
        .setIn(["apartments", "loading"], false)
        .setIn(
          ["apartments", "lst"],
          action.payload ? fromJS(action.payload) : -1
        );
    case CHANGE_COMBINE_CARD_STATUS_COMPLETE:
      return state.setIn(["detail", "data", "status"], action.payload);
    case DELETE_COMBINE_CARD:
      return state.set("deleting", true);
    case DELETE_COMBINE_CARD_COMPLETE:
      return state.set("deleting", false);
    case FETCH_MEMBER:
      return state.setIn(["members", "loading"], true);
    case FETCH_MEMBER_COMPLETE:
      return state
        .setIn(["members", "loading"], false)
        .setIn(["members", "lst2"], fromJS(action.payload || []));
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

export default combineCardDetailReducer;
