/*
 *
 * OldDebitServiceContainer reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION, FETCH_DETAIL_SERVICE,
  FETCH_DETAIL_SERVICE_COMPLETE
} from "./constants";
import { UPDATE_SERVICE_DETAIL_COMPLETE } from "./InfomationPage/constants";

export const initialState = fromJS({
  loading: false,
  data: undefined
});

function oldDebitServiceContainerReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_DETAIL_SERVICE:
      return state.set('loading', true)
    case FETCH_DETAIL_SERVICE_COMPLETE:
      return state.set('loading', false).set('data', action.payload)
    case UPDATE_SERVICE_DETAIL_COMPLETE:
      return state.set('data', !!action.payload ? action.payload : state.get('data'))
    default:
      return state;
  }
}

export default oldDebitServiceContainerReducer;
