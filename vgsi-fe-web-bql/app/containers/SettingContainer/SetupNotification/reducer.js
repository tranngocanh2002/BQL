/*
 *
 * Roles reducer
 *
 */

import { fromJS } from 'immutable';
import { DEFAULT_ACTION, FETCH_ALL_GROUP, FETCH_ALL_GROUP_COMPLETE, DELETE_GROUP, DELETE_GROUP_COMPLETE, FETCH_BUILDING_CLUSTER, FETCH_BUILDING_CLUSTER_COMPLETE, FETCH_ALL_ROLES, FETCH_ALL_ROLES_COMPLETE, UPDATE_SETTING, UPDATE_SETTING_COMPLETE } from './constants';

export const initialState = fromJS({
  loading: true,
  data: {},
  deleting: false,
  roles: {
    loading: false,
    data: []
  }
});

function rolesReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_BUILDING_CLUSTER:
    case UPDATE_SETTING:
      return state.set('loading', true)
    case UPDATE_SETTING_COMPLETE:
      return state.set('loading', false)
    case FETCH_BUILDING_CLUSTER_COMPLETE:
      return state.set('data', action.payload || {}).set('loading', false)
    case FETCH_ALL_ROLES:
      return state.setIn(['roles', 'loading'], true)
    case FETCH_ALL_ROLES_COMPLETE:
      return state.setIn(['roles', 'loading'], false).setIn(['roles', 'data'], action.payload)
    default:
      return state;
  }
}

export default rolesReducer;
