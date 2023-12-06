/*
 *
 * NotificationFeeDetail reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_APARTMENT_FEE_REMINDER, FETCH_APARTMENT_FEE_REMINDER_COMPLETE, FETCH_ANNOUNCEMENT_TEMPLATE_FEE, FETCH_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE, CREATE_NOTIFICATION_FEE_REMINDER, CREATE_NOTIFICATION_FEE_REMINDER_COMPLETE, FETCH_CATEGORY, FETCH_CATEGORY_COMPLETE, FETCH_NOTIFICATION_DETAIL, FETCH_NOTIFICATION_DETAIL_COMPLETE, FETCH_APARTMENT_SENT, FETCH_APARTMENT_SENT_COMPLETE } from "./constants";

export const initialState = fromJS({
  apartmentReminder: {
    loading: true,
    data: [],
    totalPage: 1,
    total_count: {
      total_apartment: 0,
      total_app: 0,
      total_email: 0,
      total_sms: 0
    }
  },
  template: {
    loading: true,
    data: undefined
  },
  category: {
    loading: true,
    data: []
  },
  loading: true,
  detail: undefined,
  sending: false,
  sentSuccess: false
});

function notificationFeeDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_NOTIFICATION_DETAIL: {
      return state.set('loading', true)
    }
    case FETCH_NOTIFICATION_DETAIL_COMPLETE: {
      return state.set('loading', false).set('detail', action.payload)
    }
    case FETCH_CATEGORY:
      return state.setIn(['category', 'loading'], true);
    case FETCH_CATEGORY_COMPLETE:
      return state.setIn(['category', 'loading'], false).setIn(['category', 'data'], action.payload || []);
    case CREATE_NOTIFICATION_FEE_REMINDER:
      return state.set('sending', true).set('sentSuccess', false)
    case CREATE_NOTIFICATION_FEE_REMINDER_COMPLETE:
      return state.set('sending', false).set('sentSuccess', action.payload || false)
    case FETCH_ANNOUNCEMENT_TEMPLATE_FEE: {
      return state.setIn(['template', 'loading'], true).setIn(['template', 'data'], undefined)
    }
    case FETCH_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE: {
      return state.setIn(['template', 'loading'], false).setIn(['template', 'data'], action.payload)
    }
    case FETCH_APARTMENT_FEE_REMINDER:
    case FETCH_APARTMENT_SENT:
      return state.setIn(['apartmentReminder', 'loading'], true)
    case FETCH_APARTMENT_FEE_REMINDER_COMPLETE:
    case FETCH_APARTMENT_SENT_COMPLETE:
      let data = [];
      let totalPage = 1;
      let total_count = {
        total_apartment: 0,
        total_app: 0,
        total_email: 0,
        total_sms: 0
      }

      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
        total_count = action.payload.total_count
      }

      return state.setIn(['apartmentReminder', 'loading'], false)
        .setIn(['apartmentReminder', 'data'], data)
        .setIn(['apartmentReminder', 'totalPage'], totalPage)
        .setIn(['apartmentReminder', 'total_count'], total_count)
    default:
      return state;
  }
}

export default notificationFeeDetailReducer;
