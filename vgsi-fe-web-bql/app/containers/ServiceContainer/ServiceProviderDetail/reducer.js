/*
 *
 * ServiceProviderDetail reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_DETAIL_SERVICE_PROVIDER, FETCH_DETAIL_SERVICE_PROVIDER_COMPLETE, UPDATE_SERVICE_PROVIDER, UPDATE_SERVICE_PROVIDER_COMPLETE } from "./constants";

export const initialState = fromJS({
  updating: false,
  updatedData: undefined,
  detail: {
    loading: false,
    data: undefined
  }
});

function serviceProviderDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_DETAIL_SERVICE_PROVIDER: {
      return state.setIn(['detail', 'loading'], true)
    }
    case FETCH_DETAIL_SERVICE_PROVIDER_COMPLETE: {
      return state.setIn(['detail', 'loading'], false).setIn(['detail', 'data'], action.payload ? fromJS(action.payload) : undefined)
    }
    case UPDATE_SERVICE_PROVIDER:
      return state.set('updating', true)
    case UPDATE_SERVICE_PROVIDER_COMPLETE:
      return state.set('updating', false).set('updatedData', !!action.payload ? fromJS(action.payload) : undefined)
    default:
      return state;
  }
}

export default serviceProviderDetailReducer;
