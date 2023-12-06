/*
 *
 * historyAccessControl reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_ALL_HISTORY, FETCH_ALL_HISTORY_COMPLETE, FETCH_ALL_APARTMENT, FETCH_ALL_APARTMENT_COMPLETE } from "./constants";

export const initialState = fromJS({
  loading: false,
  totalPage: 1,
  data: [],
  apartment: {
    loading: false,
    lst: []
  },
});

function historyAccessControlReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_HISTORY:
      return state.set('loading', true)
    case FETCH_ALL_HISTORY_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
      }

      return state.set('loading', false).set('deleting', false).
        set('data', fromJS(data)).set('totalPage', totalPage)
    }
    case FETCH_ALL_APARTMENT: {
      return state.setIn(['apartment', 'loading'], true)
    }
    case FETCH_ALL_APARTMENT_COMPLETE: {
      return state.setIn(['apartment', 'loading'], false)
        .setIn(['apartment', 'lst'], action.payload || [])
    }
    default:
      return state;
  }
}

export default historyAccessControlReducer;
