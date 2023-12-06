/*
 *
 * ResourceManager reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_LOG_EMAIL, FETCH_LOG_EMAIL_COMPLETE, FETCH_LOG_SMS, FETCH_LOG_SMS_COMPLETE, FETCH_LOG_NOTIFICATION, FETCH_LOG_NOTIFICATION_COMPLETE } from "./constants";

export const initialState = fromJS({
  email: {
    loading: false,
    total_count: {
      "total_limit": 0,
      "total_send": 0
    },
    pagination: {
      "totalCount": 0,
      "pageCount": 0,
      "currentPage": 0,
      "pageSize": 0
    },
    items: []
  },
  sms: {
    loading: false,
    total_count: {
      "total_limit": 0,
      "total_send": 0
    },
    pagination: {
      "totalCount": 0,
      "pageCount": 0,
      "currentPage": 0,
      "pageSize": 0
    },
    items: []
  },
  notification: {
    loading: false,
    total_count: {
      "total_limit": 0,
      "total_send": 0
    },
    pagination: {
      "totalCount": 0,
      "pageCount": 0,
      "currentPage": 0,
      "pageSize": 0
    },
    items: []
  },
});

function resourceManagerReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_LOG_EMAIL: {
      return state.setIn(['email', 'loading'], true)
    }
    case FETCH_LOG_EMAIL_COMPLETE: {
      const { items, total_count, pagination } = action.payload || {}
      if (!items) {
        return state.setIn(['email', 'loading'], false)
      }
      return state.setIn(['email', 'loading'], false)
        .setIn(['email', 'items'], items)
        .setIn(['email', 'total_count'], total_count)
        .setIn(['email', 'pagination'], pagination)
    }
    case FETCH_LOG_SMS: {
      return state.setIn(['sms', 'loading'], true)
    }
    case FETCH_LOG_SMS_COMPLETE: {
      const { items, total_count, pagination } = action.payload || {}
      if (!items) {
        return state.setIn(['sms', 'loading'], false)
      }
      return state.setIn(['sms', 'loading'], false)
        .setIn(['sms', 'items'], items)
        .setIn(['sms', 'total_count'], total_count)
        .setIn(['sms', 'pagination'], pagination)
    }
    case FETCH_LOG_NOTIFICATION: {
      return state.setIn(['notification', 'loading'], true)
    }
    case FETCH_LOG_NOTIFICATION_COMPLETE: {
      const { items, total_count, pagination } = action.payload || {}
      if (!items) {
        return state.setIn(['notification', 'loading'], false)
      }
      return state.setIn(['notification', 'loading'], false)
        .setIn(['notification', 'items'], items)
        .setIn(['notification', 'total_count'], total_count)
        .setIn(['notification', 'pagination'], pagination)
    }
    default:
      return state;
  }
}

export default resourceManagerReducer;
