/*
 *
 * FeeList reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_FEE,
  FETCH_ALL_FEE_COMPLETE,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_SERVICE_MAP,
  FETCH_SERVICE_MAP_COMPLETE,
  DELETE_FEE,
  DELETE_FEE_COMPLETE,
  UPDATE_PAYMENT,
  UPDATE_PAYMENT_COMPLETE,
  FETCH_BUILDING_AREA,
  FETCH_BUILDING_AREA_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  apartments: {
    loading: true,
    lst: [],
  },
  buildingArea: {
    loading: true,
    lst: [],
  },
  services: {
    loading: false,
    lst: [],
  },
  loading: true,
  items: [],
  totalPage: 1,
  success: true,
  deleting: false,
  updating: false,
  total_count: undefined,
});

function feeListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_BUILDING_AREA: {
      return state.setIn(["buildingArea", "loading"], true);
    }
    case FETCH_BUILDING_AREA_COMPLETE: {
      return state
        .setIn(["buildingArea", "loading"], false)
        .setIn(["buildingArea", "lst"], action.payload || []);
    }
    case FETCH_ALL_FEE:
      return state.set("loading", true);
    case FETCH_ALL_FEE_COMPLETE:
      let data = [];
      let totalPage = 1;
      let total_count = undefined;

      if (!!action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
        total_count = action.payload.total_count;
      }
      return state
        .set("loading", false)
        .set("items", fromJS(data))
        .set("totalPage", totalPage)
        .set("total_count", total_count);
    case FETCH_APARTMENT:
      return state.setIn(["apartments", "loading"], true);
    case FETCH_APARTMENT_COMPLETE:
      return state
        .setIn(["apartments", "loading"], false)
        .setIn(
          ["apartments", "lst"],
          action.payload ? fromJS(action.payload) : -1
        );
    case FETCH_SERVICE_MAP:
      return state.setIn(["services", "loading"], true);
    case FETCH_SERVICE_MAP_COMPLETE:
      return state
        .setIn(["services", "loading"], false)
        .setIn(
          ["services", "lst"],
          action.payload ? fromJS(action.payload) : -1
        );
    case DELETE_FEE:
      return state.set("deleting", true);
    case DELETE_FEE_COMPLETE:
      return state.set("deleting", false);
    case UPDATE_PAYMENT:
      return state.set("updating", true).set("success", false);
    case UPDATE_PAYMENT_COMPLETE:
      return state.set("updating", false).set("success", action.payload);
    default:
      return state;
  }
}

export default feeListReducer;
