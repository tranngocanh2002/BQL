/*
 *
 * InfomationWaterPage reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_SERVICE_PROVIDER, FETCH_SERVICE_PROVIDER_COMPLETE, UPDATE_SERVICE_DETAIL, UPDATE_SERVICE_DETAIL_COMPLETE, FETCH_WATER_CONFIG_COMPLETE } from "./constants";

export const initialState = fromJS({
  providers: {
    loading: false,
    items: []
  },
  updating: false,
  success: false,
  config: undefined
});

function infomationWaterPageReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_SERVICE_PROVIDER:
      return state.setIn(['providers', 'loading'], true)
    case FETCH_SERVICE_PROVIDER_COMPLETE:
      return state.setIn(['providers', 'loading'], false).setIn(['providers', 'items'], action.payload || [])
    case UPDATE_SERVICE_DETAIL:
      return state.set('updating', true).set('success', false)
    case UPDATE_SERVICE_DETAIL_COMPLETE:
      return state.set('updating', false).set('success', !!action.payload ? true : false)
    case FETCH_WATER_CONFIG_COMPLETE: {
      return state.set('config', action.payload)
    }
    default:
      return state;
  }
}

export default infomationWaterPageReducer;
