/*
 *
 * SupplierDetail reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_SUPPLIER_DETAIL,
  FETCH_SUPPLIER_DETAIL_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  detail: undefined,
});

function supplierDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_SUPPLIER_DETAIL:
      return state.set("loading", true);
    case FETCH_SUPPLIER_DETAIL_COMPLETE:
      return state.set("loading", false).set("detail", action.payload || -1);

    default:
      return state;
  }
}

export default supplierDetailReducer;
