/*
 *
 * BuildingInfomation reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_ALL_SERVICE, FETCH_ALL_SERVICE_COMPLETE } from "./constants";

export const initialState = fromJS({
  loading: false,
  items: [],
});

function buildingInfomationReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_SERVICE:
        return state.set('loading', true)
    case FETCH_ALL_SERVICE_COMPLETE:
        return state.set('loading', false).set('items', action.payload || [])
    default:
      return state;
  }
}

export default buildingInfomationReducer;
