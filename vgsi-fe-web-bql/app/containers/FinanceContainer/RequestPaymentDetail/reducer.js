/*
 *
 * RequestPaymentDetail reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  DELETE_REQUEST,
  DELETE_REQUEST_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  apartments: {
    loading: false,
    lst: [],
  },
  loading: false,
  items: [],
  totalPage: 1,
  deleting: false,
  deleteSuccess: false,
});

function requestPaymentReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case DELETE_REQUEST:
      return state.set("deleting", true);
    case DELETE_REQUEST_COMPLETE:
      return state.set("deleting", false).set("deleteSuccess", true);
    default:
      return state;
  }
}

export default requestPaymentReducer;
