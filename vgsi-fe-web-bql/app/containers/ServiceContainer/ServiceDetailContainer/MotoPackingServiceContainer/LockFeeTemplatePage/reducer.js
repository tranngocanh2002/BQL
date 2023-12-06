/*
 *
 * LockFeeTemplatePage reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  CREATE_PAYMENT,
  CREATE_PAYMENT_COMPLETE,
  FETCH_ALL_PAYMENT,
  FETCH_ALL_PAYMENT_COMPLETE,
  DELETE_PAYMENT,
  DELETE_PAYMENT_COMPLETE,
  UPDATE_PAYMENT,
  UPDATE_PAYMENT_COMPLETE,
  IMPORT_PAYMENT,
  IMPORT_PAYMENT_COMPLETE,
  APPROVE_PAYMENT,
  APPROVE_PAYMENT_COMPLETE,
  FETCH_LAST_MONTH_FEE,
  FETCH_LAST_MONTH_FEE_COMPLETE,
  FETCH_DESCRIPTION_FEE,
  FETCH_DESCRIPTION_FEE_COMPLETE,
  CLEAR_CACHE_MODAL,
  FETCH_VEHICLE,
  FETCH_VEHICLE_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  apartment: {
    loading: false,
    items: [],
  },
  vehicles: {
    loading: false,
    items: [],
  },
  creating: false,
  updating: false,
  deleting: false,
  success: false,
  loading: false,
  totalPage: 1,
  data: [],
  importing: false,
  importingSuccess: false,
  approving: false,
  lastMonthFee: {},
  descriptionFee: {
    loading: false,
  },
});

function lockFeeTemplatePageReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case APPROVE_PAYMENT:
      return state.set("approving", true);
    case APPROVE_PAYMENT_COMPLETE:
      return state.set("approving", false);
    case FETCH_APARTMENT:
      return state.setIn(["apartment", "loading"], true);
    case FETCH_APARTMENT_COMPLETE:
      return state
        .setIn(["apartment", "loading"], false)
        .setIn(["apartment", "items"], action.payload || []);
    case FETCH_VEHICLE:
      return state.setIn(["vehicles", "loading"], true);
    case FETCH_VEHICLE_COMPLETE:
      return state
        .setIn(["vehicles", "loading"], false)
        .setIn(["vehicles", "items"], action.payload || []);
    case CREATE_PAYMENT:
      return state.set("creating", true).set("success", false);
    case CREATE_PAYMENT_COMPLETE:
      return state
        .set("creating", false)
        .set("success", action.payload || false);

    case DELETE_PAYMENT:
      return state.set("deleting", true);
    case DELETE_PAYMENT_COMPLETE:
      return state.set("deleting", false);

    case UPDATE_PAYMENT:
      return state.set("creating", true).set("success", false);
    case UPDATE_PAYMENT_COMPLETE:
      return state
        .set("creating", false)
        .set("success", action.payload || false);

    case IMPORT_PAYMENT:
      return state.set("importing", true).set("importingSuccess", false);
    case IMPORT_PAYMENT_COMPLETE:
      return state
        .set("importing", false)
        .set("importingSuccess", action.payload || false);

    case FETCH_ALL_PAYMENT:
      return state.set("loading", true);
    case FETCH_ALL_PAYMENT_COMPLETE:
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
    case FETCH_LAST_MONTH_FEE: {
      const { apartment_id } = action.payload;
      const { lastMonthFee } = state.toJSON();
      lastMonthFee[`apa-${apartment_id}`] = {
        ...(lastMonthFee[`apa-${apartment_id}`] || {}),
        loading: true,
      };
      return state.set("lastMonthFee", lastMonthFee);
    }
    case FETCH_LAST_MONTH_FEE_COMPLETE: {
      const { apartment_id, data } = action.payload;
      const { lastMonthFee } = state.toJSON();
      lastMonthFee[`apa-${apartment_id}`] = {
        ...(lastMonthFee[`apa-${apartment_id}`] || {}),
        loading: false,
        data,
      };
      return state.set("lastMonthFee", lastMonthFee);
    }
    case FETCH_DESCRIPTION_FEE: {
      return state.setIn(["descriptionFee", "loading"], true);
    }
    case FETCH_DESCRIPTION_FEE_COMPLETE: {
      return state
        .setIn(["descriptionFee", "loading"], false)
        .setIn(["descriptionFee", "data"], action.payload);
    }
    case CLEAR_CACHE_MODAL: {
      return state
        .setIn(["descriptionFee", "loading"], true)
        .setIn(["descriptionFee", "data"], undefined)
        .set("lastMonthFee", {});
    }
    default:
      return state;
  }
}

export default lockFeeTemplatePageReducer;
