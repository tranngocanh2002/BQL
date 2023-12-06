/*
 *
 * NotificationUpdate reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION, FETCH_CATEGORY_ACTION, FETCH_CATEGORY_COMPLETE_ACTION,
  FETCH_BUILDING_AREA_ACTION, FETCH_BUILDING_AREA_COMPLETE_ACTION, FETCH_ULTILITY_ACTION, CREATE_NOTIFICATION_ACTION, CREATE_NOTIFICATION_COMPLETE_ACTION, FETCH_TOTAL_APARTMENT_ACTION, FETCH_TOTAL_APARTMENT_COMPLETE_ACTION, UPDATE_NOTIFICATION_ACTION, UPDATE_NOTIFICATION_COMPLETE_ACTION, FETCH_DETAIL_NOTIFICATION, FETCH_DETAIL_NOTIFICATION_COMPLETE
} from "./constants";

export const initialState = fromJS({
  category: {
    loading: false,
    lst: []
  },
  buildingArea: {
    loading: true,
    lst: []
  },
  creating: false,
  createSuccess: false,
  totalApartment: {
    loading: false,
    total: 0,
    building_area_ids: []
  },
  detail: {
    loading: false,
    data: undefined
  }
});

function notificationUpdateReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_TOTAL_APARTMENT_ACTION:
      return state.setIn(['totalApartment', 'loading'], true)
    case FETCH_TOTAL_APARTMENT_COMPLETE_ACTION:
      return state.setIn(['totalApartment', 'loading'], false)
        .setIn(['totalApartment', 'total'], (action.payload || {}).total_count || 0)
        .setIn(['totalApartment', 'building_area_ids'], (action.payload || {}).building_area_ids || [])
    case FETCH_ULTILITY_ACTION:
      return state.setIn(['category', 'loading'], true).setIn(['buildingArea', 'loading'], true)
    case FETCH_CATEGORY_ACTION:
      return state.setIn(['category', 'loading'], true)
    case FETCH_CATEGORY_COMPLETE_ACTION:
      return state.setIn(['category', 'loading'], false)
        .setIn(['category', 'lst'], fromJS(action.payload || []))
    case FETCH_BUILDING_AREA_ACTION:
      return state.setIn(['buildingArea', 'loading'], true)
    case FETCH_BUILDING_AREA_COMPLETE_ACTION:
      return state.setIn(['buildingArea', 'loading'], false)
        .setIn(['buildingArea', 'lst'], fromJS(action.payload || []))
    case CREATE_NOTIFICATION_ACTION:
    case UPDATE_NOTIFICATION_ACTION:
      return state.set('creating', true)
    case CREATE_NOTIFICATION_COMPLETE_ACTION:
    case UPDATE_NOTIFICATION_COMPLETE_ACTION:
      return state.set('creating', false).set('createSuccess', action.payload || false)
    case FETCH_DETAIL_NOTIFICATION: {
      return state.setIn(['detail', 'loading'], true)
    }
    case FETCH_DETAIL_NOTIFICATION_COMPLETE: {
      return state.setIn(['detail', 'loading'], false).setIn(['detail', 'data'], action.payload ? fromJS(action.payload) : undefined)
    }
    default:
      return state;
  }
}

export default notificationUpdateReducer;
