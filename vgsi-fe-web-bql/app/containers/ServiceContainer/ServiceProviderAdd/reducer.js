/*
 *
 * ServiceProviderAdd reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, CREATE_PROVIDER, CREATE_PROVIDER_COMPLETE } from "./constants";

export const initialState = fromJS({
  loading: false,
  success: false
});

function serviceProviderAddReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case CREATE_PROVIDER:
      return state.set('loading', true);
    case CREATE_PROVIDER_COMPLETE:
      return state.set('loading', false).set('success', action.payload || false);
    default:
      return state;
  }
}

export default serviceProviderAddReducer;
