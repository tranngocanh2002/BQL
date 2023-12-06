/*
 *
 * ServiceCloud reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_ALL_SERVICE_CLOUD, FETCH_ALL_SERVICE_CLOUD_COMPLETE } from "./constants";

export const initialState = fromJS({
  loading: false,
  items: []
});

function serviceCloudReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return state;
    case FETCH_ALL_SERVICE_CLOUD:
      return state.set('loading', true)
    case FETCH_ALL_SERVICE_CLOUD_COMPLETE:
      return state.set('loading', false).set('items', fromJS(action.payload || []))
    default:
      return state;
  }
}

export default serviceCloudReducer;
