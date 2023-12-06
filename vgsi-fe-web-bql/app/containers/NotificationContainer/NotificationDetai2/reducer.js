/*
 *
 * NotificationDetail reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_DETAIL_NOTIFICATION, FETCH_DETAIL_NOTIFICATION_COMPLETE, UPDATE_NOTIFICATION } from "./constants";

export const initialState = fromJS({
  loading: false,
  data: undefined,
});

function notificationDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case UPDATE_NOTIFICATION:
    case FETCH_DETAIL_NOTIFICATION: {
      return state.set('loading', true)
    }
    case FETCH_DETAIL_NOTIFICATION_COMPLETE: {
      return state.set('loading', false).set('data', action.payload ? fromJS(action.payload) : undefined)
    }
    default:
      return state;
  }
}

export default notificationDetailReducer;
