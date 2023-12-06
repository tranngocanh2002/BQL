/*
 *
 * NotificationCategory reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, CREATE_NOTIFICATION_CATEGORY_ACTION, CREATE_NOTIFICATION_CATEGORY_COMPLETE_ACTION, FETCH_NOTIFICATION_CATEGORY_COMPLETE, FETCH_NOTIFICATION_CATEGORY_ACTION, UPDATE_NOTIFICATION_CATEGORY_ACTION, UPDATE_NOTIFICATION_CATEGORY_COMPLETE, DELETE_NOTIFICATION_CATEGORY_ACTION, DELETE_NOTIFICATION_CATEGORY_COMPLETE } from "./constants";

export const initialState = fromJS({
  creating: false,
  updating: false,
  loading: false,
  totalPage: 1,
  data: [],
  deleting: false,
});

function notificationCategoryReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case CREATE_NOTIFICATION_CATEGORY_ACTION:
      return state.set('creating', true)
    case CREATE_NOTIFICATION_CATEGORY_COMPLETE_ACTION:
      return state.set('creating', false)
    case FETCH_NOTIFICATION_CATEGORY_ACTION:
      return state.set('loading', true)
    case FETCH_NOTIFICATION_CATEGORY_COMPLETE: {
      let data = [];
      let totalPage = 20;

      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
      }

      return state.set('loading', false).set('deleting', false)
        .set('data', fromJS(data)).set('totalPage', totalPage)
        .set('updating', false)
    }
    case UPDATE_NOTIFICATION_CATEGORY_ACTION:
      return state.set('updating', true)
    case UPDATE_NOTIFICATION_CATEGORY_COMPLETE:
      return state.set('updating', false)
    case DELETE_NOTIFICATION_CATEGORY_ACTION:
      return state.set('deleting', true)
    case DELETE_NOTIFICATION_CATEGORY_COMPLETE:
      return state.set('deleting', false)
    default:
      return state;
  }
}

export default notificationCategoryReducer;
