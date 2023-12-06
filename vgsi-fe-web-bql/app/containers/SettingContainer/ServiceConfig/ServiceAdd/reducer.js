/*
 *
 * ServiceAdd reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_DETAIL_SERVICE_CLOUD, FETCH_DETAIL_SERVICE_CLOUD_COMPLETE, FETCH_SERVICE_PROVIDER, FETCH_SERVICE_PROVIDER_COMPLETE, ADD_SERVICE, ADD_SERVICE_COMPLETE } from "./constants";

export const initialState = fromJS({
  detail: {
    loading: false,
    data: undefined
  },
  provider: {
    loading: false,
    items: []
  },
  adding: false,
  success: false
});

function serviceAddReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_DETAIL_SERVICE_CLOUD:
      return state.setIn(['detail', 'loading'], true)
    case FETCH_DETAIL_SERVICE_CLOUD_COMPLETE:
      return state.setIn(['detail', 'loading'], false).setIn(['detail', 'data'], action.payload)
    case FETCH_SERVICE_PROVIDER:
      return state.setIn(['provider', 'loading'], true)
    case FETCH_SERVICE_PROVIDER_COMPLETE:
      return state.setIn(['provider', 'loading'], false).setIn(['provider', 'items'], action.payload || [])
    case ADD_SERVICE:
      return state.set('adding', true)
    case ADD_SERVICE:
      return state.set('adding', true)
    case ADD_SERVICE_COMPLETE:
      return state.set('adding', false).set('success', action.payload || false)
    default:
      return state;
  }
}

export default serviceAddReducer;
