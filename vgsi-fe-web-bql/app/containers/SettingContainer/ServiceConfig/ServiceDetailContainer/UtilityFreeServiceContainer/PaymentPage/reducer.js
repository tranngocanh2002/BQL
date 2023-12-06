/*
 *
 * PaymentPage reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE, CREATE_PAYMENT, CREATE_PAYMENT_COMPLETE, FETCH_ALL_PAYMENT, FETCH_ALL_PAYMENT_COMPLETE, DELETE_PAYMENT, DELETE_PAYMENT_COMPLETE, UPDATE_PAYMENT, UPDATE_PAYMENT_COMPLETE, IMPORT_PAYMENT, IMPORT_PAYMENT_COMPLETE } from "./constants";

export const initialState = fromJS({
  apartment: {
    loading: false,
    items: []
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
});

function paymentPageReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_APARTMENT:
      return state.setIn(['apartment', 'loading'], true)
    case FETCH_APARTMENT_COMPLETE:
      return state.setIn(['apartment', 'loading'], false).setIn(['apartment', 'items'], action.payload || [])
    case CREATE_PAYMENT:
      return state.set('creating', true).set('success', false)
    case CREATE_PAYMENT_COMPLETE:
      return state.set('creating', false).set('success', action.payload || false)

    case DELETE_PAYMENT:
      return state.set('deleting', true)
    case DELETE_PAYMENT_COMPLETE:
      return state.set('deleting', false)

    case UPDATE_PAYMENT:
      return state.set('creating', true).set('success', false)
    case UPDATE_PAYMENT_COMPLETE:
      return state.set('creating', false).set('success', action.payload || false)

    case IMPORT_PAYMENT:
      return state.set('importing', true).set('importingSuccess', false)
    case IMPORT_PAYMENT_COMPLETE:
      return state.set('importing', false).set('importingSuccess', action.payload || false)

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

export default paymentPageReducer;
