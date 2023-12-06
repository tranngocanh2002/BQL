/*
 *
 * ApartmentList reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_APARTMENT,
  DELETE_APARTMENT,
  DELETE_APARTMENT_COMPLETE,
  FETCH_ALL_APARTMENT_COMPLETE,
  FETCH_BUILDING_AREA,
  FETCH_BUILDING_AREA_COMPLETE,
  UPDATE_DETAIL,
  UPDATE_DETAIL_COMPLETE,
  IMPORT_APARTMENT,
  IMPORT_APARTMENT_COMPLETE,
  FETCH_ALL_APARTMENT_TYPE,
  FETCH_ALL_APARTMENT_TYPE_COMPLETE,
  FETCH_ALL_RESIDENT_BY_PHONE,
  FETCH_ALL_RESIDENT_BY_PHONE_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  totalPage: 1,
  data: [],
  buildingArea: {
    loading: true,
    tree: [],
  },
  allResident: {
    loading: false,
    totalPage: 1,
    data: [],
  },
  updating: false,
  deleting: false,
  importing: false,
  apartment_type: {
    loading: false,
    data: [],
  },
});

function apartmentListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case IMPORT_APARTMENT:
      return state.set("importing", true);
    case IMPORT_APARTMENT_COMPLETE:
      return state.set("importing", false);
    case FETCH_ALL_APARTMENT:
      return state.set("loading", true);
    case UPDATE_DETAIL:
      return state.set("updating", true);
    case UPDATE_DETAIL_COMPLETE:
      return state.set("updating", false);
    case DELETE_APARTMENT:
      return state.set("deleting", true);
    case DELETE_APARTMENT_COMPLETE:
      return state.set("deleting", false);
    case FETCH_ALL_APARTMENT_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }

      return state
        .set("loading", false)
        .set("deleting", false)
        .set("data", fromJS(data))
        .set("totalPage", totalPage);
    }
    case FETCH_BUILDING_AREA:
      return state.setIn(["buildingArea", "loading"], true);
    case FETCH_BUILDING_AREA_COMPLETE:
      return state
        .setIn(["buildingArea", "loading"], false)
        .setIn(["buildingArea", "tree"], fromJS(action.payload || []));
    case FETCH_ALL_APARTMENT_TYPE: {
      return state.setIn(["apartment_type", "loading"], true);
    }
    case FETCH_ALL_APARTMENT_TYPE_COMPLETE: {
      return state
        .setIn(["apartment_type", "loading"], false)
        .setIn(["apartment_type", "data"], fromJS(action.payload || []));
    }
    case FETCH_ALL_RESIDENT_BY_PHONE:
      return state.setIn(["allResident", "loading"], true);
    case FETCH_ALL_RESIDENT_BY_PHONE_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }
      return state
        .setIn(["allResident", "loading"], false)
        .setIn(["allResident", "data"], fromJS(data))
        .setIn(["allResident", "totalPage"], totalPage);
    }
    default:
      return state;
  }
}

export default apartmentListReducer;
