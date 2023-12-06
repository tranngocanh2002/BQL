/*
 *
 * BillCreate reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_APARTMENT,
  FTECH_APARTMENT_COMPLETE,
  FETCH_FILTER_FEE,
  FETCH_FILTER_FEE_COMPLETE,
  RESET_FILTER_FEE,
  CREATE_BILL,
  CREATE_BILL_COMPLETE
} from "./constants";

export const initialState = fromJS({
  apartments: {
    loading: false,
    lst: []
  },
  fees: {
    loading: false,
    lst: []
  },
  bill: undefined,
  creating: false,
  success: false,
  updating: false,
});

function billCreateReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_APARTMENT:
      return state.setIn(['apartments', 'loading'], true)
    case FTECH_APARTMENT_COMPLETE:
      return state.setIn(['apartments', 'loading'], false)
        .setIn(['apartments', 'lst'], action.payload ? fromJS(action.payload) : -1)
    case FETCH_FILTER_FEE:
      return state.set('loading', true);
    case FETCH_FILTER_FEE_COMPLETE:
      return state.setIn(['fees', 'loading'], false)
        .setIn(['fees', 'lst'], action.payload ? fromJS(action.payload) : -1);
    case RESET_FILTER_FEE:
      return state.setIn(['fees', 'lst'], fromJS([]));
    case CREATE_BILL:
      return state.set('creating', true).set('success', false).set('bill', undefined);
    case CREATE_BILL_COMPLETE:
      if (!!action.payload) {
        return state.set('creating', false).set('success', true).set('bill', action.payload);
      }
      return state.set('creating', false).set('success', false);
    default:
      return state;
  }
}

export default billCreateReducer;
