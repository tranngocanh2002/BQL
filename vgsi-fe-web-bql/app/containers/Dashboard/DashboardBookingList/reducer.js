/*
 *
 * BookingList reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_BOOKING,
  FETCH_ALL_BOOKING_COMPLETE,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_SERVICE_FREE,
  FETCH_SERVICE_FREE_COMPLETE,
  FETCH_DETAIL_SERVICE_COMPLETE,
  FETCH_DETAIL_SERVICE,
  CREATE_BOOKING,
  CREATE_BOOKING_COMPLETE,
  FETCH_SLOT_FREE_COMPLETE,
  FETCH_SLOT_FREE,
  FETCH_ALL_CONFIG,
  FETCH_ALL_CONFIG_COMPLETE,
  CHECK_PRICE_BOOKING,
  CHECK_PRICE_BOOKING_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: true,
  totalPage: 1,
  data: [],
  data2: [],
  loading2: true,
  apartments: {
    loading: true,
    lst: [],
  },
  services: {
    loading: false,
    lst: [],
  },
  loading_service: true,
  servive_data: undefined,
  create: {
    loading: false,
    data: {},
    success: false,
  },
  freeSlot: {},
  price: {
    loading: false,
    data: undefined,
  },
});

function bookingListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_BOOKING:
      return state.set("loading", true);
    case FETCH_ALL_BOOKING_COMPLETE: {
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
    case CREATE_BOOKING:
      return state
        .setIn(["create", "loading"], true)
        .setIn(["create", "success"], false);
    case CREATE_BOOKING_COMPLETE:
      return state
        .setIn(["create", "loading"], false)
        .setIn(
          ["create", "success"],
          action.payload ? action.payload.status : false
        )
        .setIn(["create", "data"], action.payload ? action.payload.data : {});
    case FETCH_SLOT_FREE: {
      const { service_utility_config_id, current_time } = action.payload;
      let freeSlot = state.get("freeSlot");
      freeSlot = freeSlot.set(
        `slot-${service_utility_config_id}-${current_time}`,
        {
          loading: true,
          items: [],
        }
      );
      return state.set("freeSlot", freeSlot);
    }
    case FETCH_SLOT_FREE_COMPLETE: {
      const { service_utility_config_id, current_time, items } = action.payload;
      let freeSlot = state.get("freeSlot");
      freeSlot = freeSlot.set(
        `slot-${service_utility_config_id}-${current_time}`,
        {
          loading: false,
          items,
        }
      );
      return state.set("freeSlot", freeSlot);
    }
    case CHECK_PRICE_BOOKING:
      return state.setIn(["price", "loading"], true);
    case CHECK_PRICE_BOOKING_COMPLETE:
      return state
        .setIn(["price", "loading"], false)
        .setIn(["price", "data"], action.payload || false);

    case FETCH_ALL_CONFIG:
      return state.set("loading2", true).set("data2", []);
    case FETCH_ALL_CONFIG_COMPLETE:
      return state.set("loading2", false).set("data2", action.payload || []);

    default:
      return state;
  }
}

export default bookingListReducer;
