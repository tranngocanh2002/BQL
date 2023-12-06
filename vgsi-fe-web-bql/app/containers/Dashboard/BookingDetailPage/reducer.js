/*
 *
 * BookingDetail reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_DETAIL_BOOKING,
  FETCH_DETAIL_BOOKING_COMPLETE,
  FETCH_SERVICE_FREE,
  FETCH_SERVICE_FREE_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  detail: {
    loading: false,
    data: undefined,
  },
  services: {
    loading: false,
    listService: [],
  },
});

function bookingDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return state;
    case FETCH_DETAIL_BOOKING:
      return state.setIn(["detail", "loading"], true);
    case FETCH_DETAIL_BOOKING_COMPLETE:
      return state
        .setIn(["detail", "loading"], false)
        .setIn(["detail", "data"], action.payload);
    case FETCH_SERVICE_FREE:
      return state.setIn(["services", "loading"], true);
    case FETCH_SERVICE_FREE_COMPLETE:
      return state
        .setIn(["services", "loading"], false)
        .setIn(
          ["services", "listService"],
          action.payload ? fromJS(action.payload) : -1
        );
    default:
      return state;
  }
}

export default bookingDetailReducer;
