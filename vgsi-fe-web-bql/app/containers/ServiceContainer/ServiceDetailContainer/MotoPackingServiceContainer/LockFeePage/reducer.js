/*
 *
 * LockFeePage reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE, CREATE_PAYMENT, CREATE_PAYMENT_COMPLETE, FETCH_ALL_PAYMENT, FETCH_ALL_PAYMENT_COMPLETE, DELETE_PAYMENT, DELETE_PAYMENT_COMPLETE, UPDATE_PAYMENT, UPDATE_PAYMENT_COMPLETE, IMPORT_PAYMENT, IMPORT_PAYMENT_COMPLETE } from "./constants";

export const initialState = fromJS({
  apartment: {
    loading: false,
    items: []
  },
  loading: false,
  totalPage: 1,
  data: [],
});

function lockFeePageReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_APARTMENT:
      return state.setIn(['apartment', 'loading'], true)
    case FETCH_APARTMENT_COMPLETE:
      return state.setIn(['apartment', 'loading'], false).setIn(['apartment', 'items'], action.payload || [])
    case FETCH_ALL_PAYMENT:
      return state.set('loading', true)
    case FETCH_ALL_PAYMENT_COMPLETE:
      let data = [];
      let totalPage = 1;

      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
      }

      return state.set('loading', false)
        .set('data', fromJS(data)).set('totalPage', totalPage).set('deleting', false)
    default:
      return state;
  }
}

export default lockFeePageReducer;
