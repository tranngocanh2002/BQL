/*
 *
 * ResidentList reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_ALL_OLD_RESIDENT, FETCH_ALL_OLD_RESIDENT_COMPLETE } from "./constants";

export const initialState = fromJS({
  loading: false,
  totalPage: 1,
  data: [],
});

function residentOldListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_OLD_RESIDENT:
      return state.set('loading', true)
    case FETCH_ALL_OLD_RESIDENT_COMPLETE: {
      let data = [];
      let totalPage = 1;
      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
      }
      return state.set('loading', false).
        set('data', fromJS(data)).set('totalPage', totalPage)
    }
    default:
      return state;
  }
}

export default residentOldListReducer;
