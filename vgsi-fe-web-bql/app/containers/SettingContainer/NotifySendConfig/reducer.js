/*
 *
 * Notify Send Config reducer
 *
 */

import { fromJS } from 'immutable';
import { DEFAULT_ACTION, FETCH_ALL_NOTIFY_SEND_CONFIG, FETCH_ALL_NOTIFY_SEND_CONFIG_COMPLETE, UPDATE_NOTIFY_SEND_CONFIG, UPDATE_NOTIFY_SEND_CONFIG_COMPLETE, UPDATE_ALL_NOTIFY_SEND_CONFIG, UPDATE_ALL_NOTIFY_SEND_CONFIG_COMPLETE } from './constants';

export const initialState = fromJS({
  loading: false,
  update_all: false,
  sends: {
    loading: false,
    data: []
  }
});

function sendsReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_NOTIFY_SEND_CONFIG:
      return state.setIn(['sends', 'loading'], true)
    case FETCH_ALL_NOTIFY_SEND_CONFIG_COMPLETE:
      return state.setIn(['sends', 'loading'], false).setIn(['sends', 'data'], action.payload)
    case UPDATE_NOTIFY_SEND_CONFIG:
      return state.set('loading', true)
    case UPDATE_NOTIFY_SEND_CONFIG_COMPLETE:
      return state.set('loading', action.payload)
    case UPDATE_ALL_NOTIFY_SEND_CONFIG:
      return state.set('update_all', true)
    case UPDATE_ALL_NOTIFY_SEND_CONFIG_COMPLETE:
      return state.set('update_all', action.payload)
    default:
      return state;
  }
}

export default sendsReducer;
