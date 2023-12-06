/*
 *
 * FeeList reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION, FETCH_ALL_FEE, FETCH_ALL_FEE_COMPLETE,
  FETCH_SERVICE_MAP, FETCH_SERVICE_MAP_COMPLETE,
  DELETE_FEE, DELETE_FEE_COMPLETE,
  UPDATE_PAYMENT, UPDATE_PAYMENT_COMPLETE
} from "./constants";

export const initialState = fromJS({
  services: {
    loading: false,
    lst: []
  },
  loading: false,
  items: [],
	totalPage: 1,
  success: true,
	deleting: false,
  updating: false
});

function feeListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return state;

    case FETCH_ALL_FEE:
      return state.set('loading', true)
    case FETCH_ALL_FEE_COMPLETE:
      let data = [];
      let totalPage = 1;

      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
      }
      return state.set('loading', false).set('items', fromJS(data)).set('totalPage', totalPage);
    case FETCH_SERVICE_MAP:
      return state.setIn(['services', 'loading'], true);
    case FETCH_SERVICE_MAP_COMPLETE:
      return state.setIn(['services', 'loading'], false)
        .setIn(['services', 'lst'], action.payload ? fromJS(action.payload) : -1)
	  case DELETE_FEE:
		  return state.set('deleting', true);
	  case DELETE_FEE_COMPLETE:
		  return state.set('deleting', false);
    case UPDATE_PAYMENT:
      return state.set('updating', true).set('success', false);
    case UPDATE_PAYMENT_COMPLETE:
      return state.set('updating', false).set('success', action.payload);
    default:
      return state;
  }
}

export default feeListReducer;
