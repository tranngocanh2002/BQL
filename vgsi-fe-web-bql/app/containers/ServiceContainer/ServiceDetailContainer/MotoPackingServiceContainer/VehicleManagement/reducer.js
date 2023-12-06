/*
 *
 * VihicleManagement reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_VEHICLE,
  FETCH_ALL_VEHICLE_COMPLETE,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_ALL_FEE_LEVEL,
  FETCH_ALL_FEE_LEVEL_COMPLETE,
  CREATE_VEHICLE,
  UPDATE_VEHICLE,
  DELETE_VEHICLE,
  CREATE_VEHICLE_COMPLETE,
  UPDATE_VEHICLE_COMPLETE,
  DELETE_VEHICLE_COMPLETE,
  IMPORT_VEHICLE,
  IMPORT_VEHICLE_COMPLETE,
  ACTIVE_VEHICLE,
  ACTIVE_VEHICLE_COMPLETE,
  CANCEL_VEHICLE,
  CANCEL_VEHICLE_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  data: [],
  totalPage: 0,
  apartment: {
    loading: false,
    items: [],
  },
  feeLevel: {
    loading: false,
    items: [],
  },
  creating: false,
  updating: false,
  deleting: false,
  activing: false,
  cancling: false,
  success: false,
  importing: false,
  importSuccess: false,
});

function vihicleManagementReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case IMPORT_VEHICLE:
      return state.set("importing", true).set("importSuccess", false);
    case IMPORT_VEHICLE_COMPLETE:
      return state
        .set("importing", false)
        .set("importSuccess", action.payload || false);
    case CREATE_VEHICLE: {
      return state.set("creating", true).set("success", false);
    }
    case UPDATE_VEHICLE: {
      return state.set("updating", true).set("success", false);
    }
    case DELETE_VEHICLE: {
      return state.set("deleting", true).set("success", false);
    }
    case ACTIVE_VEHICLE: {
      return state.set("activing", true).set("success", false);
    }
    case CANCEL_VEHICLE: {
      return state.set("cancling", true).set("success", false);
    }
    case CREATE_VEHICLE_COMPLETE: {
      return state
        .set("creating", false)
        .set(
          "success",
          !!action.payload && action.payload.success
            ? action.payload.success
            : false
        );
    }
    case UPDATE_VEHICLE_COMPLETE: {
      return state
        .set("updating", false)
        .set(
          "success",
          !!action.payload && action.payload.success
            ? action.payload.success
            : false
        );
    }
    case DELETE_VEHICLE_COMPLETE: {
      return state
        .set("deleting", false)
        .set("success", action.payload || false);
    }
    case ACTIVE_VEHICLE_COMPLETE: {
      return state
        .set("activing", false)
        .set(
          "success",
          !!action.payload && action.payload.success
            ? action.payload.success
            : false
        );
    }
    case CANCEL_VEHICLE_COMPLETE: {
      return state
        .set("cancling", false)
        .set(
          "success",
          !!action.payload && action.payload.success
            ? action.payload.success
            : false
        );
    }

    case FETCH_APARTMENT:
      return state.setIn(["apartment", "loading"], true);
    case FETCH_APARTMENT_COMPLETE:
      return state
        .setIn(["apartment", "loading"], false)
        .setIn(["apartment", "items"], action.payload || []);
    case FETCH_ALL_FEE_LEVEL:
      return state.setIn(["feeLevel", "loading"], true);
    case FETCH_ALL_FEE_LEVEL_COMPLETE:
      return state
        .setIn(["feeLevel", "loading"], false)
        .setIn(["feeLevel", "items"], action.payload || []);

    case FETCH_ALL_VEHICLE:
      return state.set("loading", true);
    case FETCH_ALL_VEHICLE_COMPLETE:
      let data = [];
      let totalPage = 1;

      if (!!action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }

      return state
        .set("loading", false)
        .set("data", fromJS(data))
        .set("totalPage", totalPage)
        .set("deleting", false);
    default:
      return state;
  }
}

export default vihicleManagementReducer;
