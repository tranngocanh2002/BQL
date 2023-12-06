/*
 *
 * InfoUsage reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE, FETCH_USAGE, FETCH_USAGE_COMPLETE, IMPORT_USAGE, IMPORT_USAGE_COMPLETE, ADD_INFO_COMPLETE, UPDATE_INFO, UPDATE_INFO_COMPLETE, DELETE_INFO, DELETE_INFO_COMPLETE, ADD_INFO } from "./constants";

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

function infoUsageReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_APARTMENT:
      return state.setIn(['apartment', 'loading'], true)
    case FETCH_APARTMENT_COMPLETE:
      return state.setIn(['apartment', 'loading'], false).setIn(['apartment', 'items'], action.payload || [])
    case FETCH_USAGE:
      return state.set('loading', true)
    case FETCH_USAGE_COMPLETE:
      let data = [];
      let totalPage = 1;

      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
      }

      return state.set('loading', false)
        .set('data', fromJS(data)).set('totalPage', totalPage).set('deleting', false)

    case IMPORT_USAGE:
      return state.set('importing', true).set('importingSuccess', false)
    case IMPORT_USAGE_COMPLETE:
      return state.set('importing', false).set('importingSuccess', action.payload || false)

    case ADD_INFO:
      return state.set('creating', true).set('success', false)
    case ADD_INFO_COMPLETE:
      return state.set('creating', false).set('success', action.payload || false)

    case UPDATE_INFO:
      return state.set('updating', true).set('success', false)
    case UPDATE_INFO_COMPLETE:
      return state.set('updating', false).set('success', action.payload || false)

    case DELETE_INFO:
      return state.set('deleting', true).set('success', false)
    case DELETE_INFO_COMPLETE:
      return state.set('deleting', false).set('success', action.payload || false)

    default:
      return state;
  }
}

export default infoUsageReducer;
