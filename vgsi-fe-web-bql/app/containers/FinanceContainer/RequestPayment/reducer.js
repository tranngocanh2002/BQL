/*
 *
 * RequestPayment reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE, FETCH_PAYMENT_REQUEST, FETCH_PAYMENT_REQUEST_COMPLETE, DELETE_REQUEST, DELETE_REQUEST_COMPLETE } from "./constants";

export const initialState = fromJS({
  apartments: {
    loading: false,
    lst: []
  },
  loading: false,
  items: [],
  totalPage: 1,
  deleting: false
});

function requestPaymentReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_APARTMENT:
      return state.setIn(['apartments', 'loading'], true)
    case FETCH_APARTMENT_COMPLETE:
      return state.setIn(['apartments', 'loading'], false)
        .setIn(['apartments', 'lst'], action.payload ? fromJS(action.payload) : -1)
    case FETCH_PAYMENT_REQUEST:
      return state.set('loading', true)
    case FETCH_PAYMENT_REQUEST_COMPLETE:
      let data = [];
      let totalPage = 1;

      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
      }
      return state.set('loading', false)
        .set('items', fromJS(data)).set('totalPage', totalPage)
    case DELETE_REQUEST:
      return state.set('deleting', true)
    case DELETE_REQUEST_COMPLETE:
      return state.set('deleting', false)
    default:
      return state;
  }
}

export default requestPaymentReducer;
