/*
 *
 * ServiceBookingFeeList reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_BOOKING_FEE,
  FETCH_ALL_BOOKING_FEE_COMPLETE,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_SERVICE_FREE,
  FETCH_SERVICE_FREE_COMPLETE,
  FETCH_DETAIL_SERVICE_COMPLETE,
  FETCH_DETAIL_SERVICE
} from "./constants";

export const initialState = fromJS({
  loading: true,
  totalPage: 1,
  data: [],
  apartments: {
    loading: true,
    lst: []
  },
  services: {
    loading: false,
    lst: []
  },
  loading_service: true,
  servive_data: undefined
});

function serviceBookingListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_BOOKING_FEE:
      return state.set("loading", true);
    case FETCH_ALL_BOOKING_FEE_COMPLETE: {
      let data = [];
      let totalPage = 1;
      if (!!action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }
      return state
        .set("loading", false)
        .set("data", fromJS(data))
        .set("totalPage", totalPage);
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
    case FETCH_SERVICE_FREE:
      return state.setIn(["services", "loading"], true);
    case FETCH_SERVICE_FREE_COMPLETE:
      return state
        .setIn(["services", "loading"], false)
        .setIn(
          ["services", "lst"],
          action.payload ? fromJS(action.payload) : -1
        );
    case FETCH_DETAIL_SERVICE:
      return state.set("loading_service", true);
    case FETCH_DETAIL_SERVICE_COMPLETE:
      return state
        .set("loading_service", false)
        .set("servive_data", action.payload);
    default:
      return state;
  }
}

export default serviceBookingListReducer;
