/*
 *
 * NotificationFeeList reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_ALL_NOTIFICATION_FEE_ACTION, FETCH_ALL_NOTIFICATION_FEE_COMPLETE_ACTION, FETCH_NOTIFICATION_CATEGORY_ACTION, FETCH_NOTIFICATION_CATEGORY_COMPLETE } from "./constants";

export const initialState = fromJS({
  loading: false,
  totalPage: 1,
  data: [],
  deleting: false,
});

function notificationFeeListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_NOTIFICATION_FEE_ACTION:
      return state.set('loading', true)
    case FETCH_ALL_NOTIFICATION_FEE_COMPLETE_ACTION:
      let data = [];
      let totalPage = 1;

      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
      }

      return state.set('loading', false).set('deleting', false)
        .set('data', fromJS(data)).set('totalPage', totalPage)
        .set('updating', false)
    default:
      return state;
  }
}

export default notificationFeeListReducer;
