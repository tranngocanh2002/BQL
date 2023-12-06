/*
 *
 * AccountBase reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_DETAIL, FETCH_DETAIL_COMPLETE, UPDATE_INFO, UPDATE_INFO_COMPLETE } from "./constants";

export const initialState = fromJS({
  detail: {
    loading: false,
    data: null
  },
  updating: false,
  updateSuccess: false,
});

function accountBaseReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_DETAIL:
      return state.setIn(['detail', 'loading'], true)
    case FETCH_DETAIL_COMPLETE:
      return state.
        setIn(['detail', 'loading'], !!!action.payload).
        setIn(['detail', 'data'], action.payload)
    case UPDATE_INFO:
      return state.set('updating', true).set('updateSuccess', false)
    case UPDATE_INFO_COMPLETE:
      return state.set('updating', false).setIn(['detail', 'data'], action.payload || state.get('detail').toJS().data).set('updateSuccess', !!action.payload)
    default:
      return state;
  }
}

export default accountBaseReducer;
