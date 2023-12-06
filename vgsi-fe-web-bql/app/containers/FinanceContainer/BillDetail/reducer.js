/*
 *
 * BillDetail reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_DETAIL_BILL,
  FETCH_DETAIL_BILL_COMPLETE,
  DELETE_BILL,
  DELETE_BILL_COMPLETE,
  UPDATE_BILL, UPDATE_BILL_COMPLETE,
  UPDATE_BILL_STATUS, UPDATE_BILL_STATUS_COMPLETE
} from "./constants";

export const initialState = fromJS({
  detail: {
    loading: false,
    data: undefined
  },
  updating: false,
  status_updating: false,
  success: false
});

function billDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return state;

    case FETCH_DETAIL_BILL:
      return state.setIn(['detail', 'loading'], true);
    case FETCH_DETAIL_BILL_COMPLETE:
      return state.setIn(['detail', 'loading'], false).setIn(['detail', 'data'], action.payload ? fromJS(action.payload) : -1);
    case UPDATE_BILL:
      return state.set('updating', true);
    case UPDATE_BILL_COMPLETE:
      return state.set('updating', false).set('success', action.payload);
    case UPDATE_BILL_STATUS:
      return state.set('status_updating', true);
    case UPDATE_BILL_STATUS_COMPLETE:
      return state.set('status_updating', false).set('success', action.payload);
    default:
      return state;
  }
}

export default billDetailReducer;
