/*
 *
 * ResidentOldDetail reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_DETAIL_OLD_RESIDENT_COMPLETE, FETCH_DETAIL_OLD_RESIDENT } from "./constants";

export const initialState = fromJS({
  detail: {
    loading: false,
    data: undefined
  },
});

function residentOldDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_DETAIL_OLD_RESIDENT:
      return state.setIn(['detail', 'loading'], true)
    case FETCH_DETAIL_OLD_RESIDENT_COMPLETE:
      return state.setIn(['detail', 'loading'], false).setIn(['detail', 'data'], action.payload ? fromJS(action.payload) : -1)
    default:
      return state;
  }
}

export default residentOldDetailReducer;
