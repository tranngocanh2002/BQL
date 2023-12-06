/*
 *
 * SupplierList reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  DELETE_SUPPLIER,
  DELETE_SUPPLIER_COMPLETE,
  FETCH_ALL_SUPPLIER,
  FETCH_ALL_SUPPLIER_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  totalPage: 1,
  data: [],
  deleting: false,
});

function supplierListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_SUPPLIER:
      return state.set("loading", true);
    case FETCH_ALL_SUPPLIER_COMPLETE: {
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
    case DELETE_SUPPLIER:
      return state.set("deleting", true);
    case DELETE_SUPPLIER_COMPLETE:
      return state.set("deleting", false);
    default:
      return state;
  }
}

export default supplierListReducer;
