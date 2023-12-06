/*
 *
 * NotificationList reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  DELETE_NOTIFICATION_ACTION,
  DELETE_NOTIFICATION_COMPLETE_ACTION,
  FETCH_ALL_NOTIFICATIOIN_ACTION,
  FETCH_ALL_NOTIFICATIOIN_COMPLETE_ACTION,
  FETCH_NOTIFICATION_CATEGORY_ACTION,
  FETCH_NOTIFICATION_CATEGORY_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  totalPage: 1,
  data: [],
  deleting: false,
  category: {
    loading: false,
    lst: [],
  },
});

function notificationListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_NOTIFICATIOIN_ACTION:
      return state.set("loading", true);
    case FETCH_ALL_NOTIFICATIOIN_COMPLETE_ACTION: {
      let data = [];
      let totalPage = 1;

      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }

      return state
        .set("loading", false)
        .set("deleting", false)
        .set("data", fromJS(data))
        .set("totalPage", totalPage)
        .set("updating", false);
    }
    case FETCH_NOTIFICATION_CATEGORY_ACTION:
      return state.setIn(["category", "loading"], true);
    case FETCH_NOTIFICATION_CATEGORY_COMPLETE:
      return state
        .setIn(["category", "loading"], false)
        .setIn(["category", "lst"], fromJS(action.payload || []));
    case DELETE_NOTIFICATION_ACTION:
      return state.set("deleting", true);
    case DELETE_NOTIFICATION_COMPLETE_ACTION:
      return state.set("deleting", false);
    default:
      return state;
  }
}

export default notificationListReducer;
