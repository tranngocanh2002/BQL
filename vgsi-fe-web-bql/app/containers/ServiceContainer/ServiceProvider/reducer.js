/*
 *
 * ServiceProvider reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_PROVIDERS, FETCH_PROVIDERS_COMPLETE, DELETE_SERVICE_PROVIDER, DELETE_SERVICE_PROVIDER_COMPLETE } from "./constants";

export const initialState = fromJS({
  loading: false,
  totalPage: 1,
  data: [],
  deleting: false,
});

function serviceProviderReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_PROVIDERS:
      return state.set('loading', true)
    case FETCH_PROVIDERS_COMPLETE:
      let data = [];
      let totalPage = 1;

      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
      }

      return state.set('loading', false).set('deleting', false)
        .set('data', fromJS(data)).set('totalPage', totalPage)
        .set('updating', false)
    case DELETE_SERVICE_PROVIDER:
      return state.set('deleting', true)
    case DELETE_SERVICE_PROVIDER_COMPLETE:
      return state.set('deleting', false)
    default:
      return state;
  }
}

export default serviceProviderReducer;
