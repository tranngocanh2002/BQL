/*
 *
 * TicketList reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_TICKET,
  FETCH_ALL_TICKET_COMPLETE,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_CATEGORY,
  FETCH_CATEGORY_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  categories: {
    loading: false,
    lst: [],
  },
  apartments: {
    loading: false,
    lst: [],
  },
  totalPage: 1,
  data: [],
  buildingArea: {
    loading: true,
    tree: [],
  },
  updating: false,
  deleting: false,
});

function ticketListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_TICKET:
      return state.set("loading", true);
    case FETCH_ALL_TICKET_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }
      return state
        .set("loading", false)
        .set("data", fromJS(data))
        .set("totalPage", totalPage)
        .set("deleting", false)
        .set("updating", false)
        .set("loading", false);
    }
    case FETCH_APARTMENT:
      return state.setIn(["apartments", "loading"], true);
    case FETCH_APARTMENT_COMPLETE:
      return state
        .setIn(["apartments", "loading"], false)
        .setIn(
          ["apartments", "lst"],
          action.payload ? fromJS(action.payload) : -1
        );
    case FETCH_CATEGORY:
      return state.setIn(["categories", "loading"], true);
    case FETCH_CATEGORY_COMPLETE: {
      return state
        .setIn(["categories", "loading"], false)
        .setIn(
          ["categories", "lst"],
          action.payload ? fromJS(action.payload) : -1
        );
    }
    default:
      return state;
  }
}

export default ticketListReducer;
