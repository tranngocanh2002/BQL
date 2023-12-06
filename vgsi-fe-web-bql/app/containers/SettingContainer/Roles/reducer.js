/*
 *
 * Roles reducer
 *
 */

import { fromJS } from 'immutable';
import { DEFAULT_ACTION, FETCH_ALL_GROUP, FETCH_ALL_GROUP_COMPLETE, DELETE_GROUP, DELETE_GROUP_COMPLETE } from './constants';

export const initialState = fromJS({
  loading: false,
  data: [],
  deleting: false
});

function rolesReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return state;
    case FETCH_ALL_GROUP:
      return state.set('loading', true)
    case DELETE_GROUP:
      return state.set('deleting', true)
    case DELETE_GROUP_COMPLETE:
      return state.set('deleting', false)
    case FETCH_ALL_GROUP_COMPLETE:
      return state.set('loading', false).set('data', action.payload || []).set('deleting', false)
    default:
      return state;
  }
}

export default rolesReducer;
