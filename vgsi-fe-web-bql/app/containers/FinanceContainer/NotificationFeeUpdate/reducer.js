/*
 *
 * NotificationFeeUpdate reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_APARTMENT_FEE_REMINDER, FETCH_APARTMENT_FEE_REMINDER_COMPLETE, FETCH_ANNOUNCEMENT_TEMPLATE_FEE, FETCH_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE, CREATE_NOTIFICATION_FEE_REMINDER, CREATE_NOTIFICATION_FEE_REMINDER_COMPLETE, FETCH_CATEGORY, FETCH_CATEGORY_COMPLETE, FETCH_BUILDING_AREA_ACTION, FETCH_BUILDING_AREA_COMPLETE_ACTION, FETCH_APARTMENT_SENT, FETCH_APARTMENT_SENT_COMPLETE, FETCH_DETAIL_ANNOUNCEMENT, FETCH_DETAIL_ANNOUNCEMENT_COMPLETE, UPDATE_NOTIFICATION_ACTION, UPDATE_NOTIFICATION_COMPLETE_ACTION } from "./constants";

export const initialState = fromJS({
  apartmentToSend: {
    loading: false,
    data: [],
    totalPage: 0,
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
  buildingArea: {
    loading: true,
    lst: []
  },
  creating: false,
  createSuccess: false,
  loading: true,
  detail: undefined
});

function notificationFeeUpdateReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_BUILDING_AREA_ACTION:
      return state.setIn(['buildingArea', 'loading'], true)
    case FETCH_BUILDING_AREA_COMPLETE_ACTION:
      return state.setIn(['buildingArea', 'loading'], false)
        .setIn(['buildingArea', 'lst'], fromJS(action.payload || []))
    case FETCH_CATEGORY:
      return state.setIn(['category', 'loading'], true);
    case FETCH_CATEGORY_COMPLETE:
      return state.setIn(['category', 'loading'], false).setIn(['category', 'data'], action.payload || []);
    case UPDATE_NOTIFICATION_ACTION:
      return state.set('creating', true).set('createSuccess', false)
    case UPDATE_NOTIFICATION_COMPLETE_ACTION:
      return state.set('creating', false).set('createSuccess', action.payload || false)
    case FETCH_DETAIL_ANNOUNCEMENT:
      return state.set('loading', true)
    case FETCH_DETAIL_ANNOUNCEMENT_COMPLETE:
      return state.set('loading', false).set('detail', action.payload)
    case FETCH_ANNOUNCEMENT_TEMPLATE_FEE: {
      return state.setIn(['template', 'loading'], true).setIn(['template', 'data'], undefined)
    }
    case FETCH_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE: {
      return state.setIn(['template', 'loading'], false).setIn(['template', 'data'], action.payload)
    }
    case FETCH_APARTMENT_SENT:
      return state.setIn(['apartmentToSend', 'loading'], true)
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

      return state.setIn(['apartmentToSend', 'loading'], false)
        .setIn(['apartmentToSend', 'data'], data)
        .setIn(['apartmentToSend', 'totalPage'], totalPage)
        .setIn(['apartmentToSend', 'total_count'], total_count)
    default:
      return state;
  }
}

export default notificationFeeUpdateReducer;
