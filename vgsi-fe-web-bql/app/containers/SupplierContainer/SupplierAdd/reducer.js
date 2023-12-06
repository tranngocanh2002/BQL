/*
 *
 * SupplierAdd reducer
 *
 */

import { fromJS } from "immutable";
import {
  CREATE_SUPPLIER,
  CREATE_SUPPLIER_COMPLETE,
  DEFAULT_ACTION,
  FETCH_SUPPLIER_DETAIL,
  FETCH_SUPPLIER_DETAIL_COMPLETE,
  UPDATE_SUPPLIER,
  UPDATE_SUPPLIER_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  creating: false,
  detail: {
    loading: false,
    data: undefined,
  },
  updating: false,
  success: false,
  updateSuccess: false,
});

function supplierAddReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case CREATE_SUPPLIER:
      return state.set("creating", true);
    case CREATE_SUPPLIER_COMPLETE:
      return state
        .set("creating", false)
        .set("success", action.payload || false);
    case UPDATE_SUPPLIER:
      return state.set("updating", true);
    case UPDATE_SUPPLIER_COMPLETE:
      return state
        .set("updating", false)
        .set("updateSuccess", action.payload || false);
    case FETCH_SUPPLIER_DETAIL:
      return state.setIn(["detail", "loading"], true);
    case FETCH_SUPPLIER_DETAIL_COMPLETE:
      return state
        .setIn(["detail", "loading"], false)
        .setIn(
          ["detail", "data"],
          action.payload ? fromJS(action.payload) : -1
        );
    default:
      return state;
  }
}

export default supplierAddReducer;
