/*
 *
 * DashboardInvoiceBill reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_ALL_FEE_OF_APARTMENT,
  FETCH_ALL_FEE_OF_APARTMENT_COMPLETE,
  CREATE_ORDER,
  CREATE_ORDER_COMPLETE,
  CLEAR_FORM,
  FETCH_MEMBER,
  FETCH_MEMBER_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  apartment: {
    loading: true,
    items: [],
  },
  fee: {
    loading: false,
    items: [],
    total_count: {
      total_money_collected: 0,
      total_more_money_collecte: 0,
      total_price: 0,
    },
  },
  creating: false,
  createData: undefined,
  members: {
    loading: true,
    lst: [],
  },
});

function dashboardInvoiceBillReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case CLEAR_FORM:
      return state.set("creating", false).set("createData", undefined);
    case FETCH_APARTMENT:
      return state.setIn(["apartment", "loading"], true);
    case FETCH_APARTMENT_COMPLETE:
      return state
        .setIn(["apartment", "loading"], false)
        .setIn(["apartment", "items"], action.payload || []);
    case FETCH_ALL_FEE_OF_APARTMENT:
      return state.setIn(["fee", "loading"], true);
    case FETCH_ALL_FEE_OF_APARTMENT_COMPLETE:
      return state
        .setIn(["fee", "loading"], false)
        .setIn(["fee", "items"], action.payload.items)
        .setIn(["fee", "total_count"], action.payload.total_count);
    case CREATE_ORDER:
      return state.set("creating", true).set("createData", undefined);
    case CREATE_ORDER_COMPLETE:
      return state.set("creating", false).set("createData", action.payload);
    case FETCH_MEMBER:
      return state.setIn(["members", "loading"], true);
    case FETCH_MEMBER_COMPLETE:
      return state
        .setIn(["members", "loading"], false)
        .setIn(["members", "lst"], fromJS(action.payload || []))
        .set("removing", false)
        .set("addingMember", false);
    default:
      return state;
  }
}

export default dashboardInvoiceBillReducer;
