/*
 *
 * NotificationAdd reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  CREATE_NOTIFICATION_FEE_REMINDER,
  CREATE_NOTIFICATION_FEE_REMINDER_COMPLETE,
  FETCH_CATEGORY,
  FETCH_CATEGORY_COMPLETE,
  FETCH_BUILDING_AREA_ACTION,
  FETCH_BUILDING_AREA_COMPLETE_ACTION,
  FETCH_APARTMENT_SENT,
  FETCH_APARTMENT_SENT_COMPLETE,
  FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  SHOW_CHOOSE_TEMPLATE_LIST,
} from "./constants";

export const initialState = fromJS({
  apartmentToSend: {
    loading: false,
    data: [],
    totalPage: 0,
    total_count: {
      total_apartment: 0,
      total_app: 0,
      total_email: 0,
      total_sms: 0,
    },
  },
  template_list: {
    loading: false,
    data: [],
    totalPage: 1,
  },
  template: {
    loading: true,
    data: undefined,
  },
  category: {
    loading: true,
    data: [],
  },
  buildingArea: {
    loading: true,
    lst: [],
  },
  showChooseTemplate: false,
  creating: false,
  createSuccess: false,
});

function notificationAddReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case SHOW_CHOOSE_TEMPLATE_LIST:
      return state.set("showChooseTemplate", action.payload);
    case FETCH_BUILDING_AREA_ACTION:
      return state.setIn(["buildingArea", "loading"], true);
    case FETCH_BUILDING_AREA_COMPLETE_ACTION:
      return state
        .setIn(["buildingArea", "loading"], false)
        .setIn(["buildingArea", "lst"], fromJS(action.payload || []));
    case FETCH_CATEGORY:
      return state.setIn(["category", "loading"], true);
    case FETCH_CATEGORY_COMPLETE:
      return state
        .setIn(["category", "loading"], false)
        .setIn(["category", "data"], action.payload || []);
    case CREATE_NOTIFICATION_FEE_REMINDER:
      return state.set("creating", true).set("createSuccess", false);
    //XXX: 6 reducer case to update state
    case CREATE_NOTIFICATION_FEE_REMINDER_COMPLETE:
      return state
        .set("creating", false)
        .set("createSuccess", action.payload || false);
    case FETCH_ANNOUNCEMENT_TEMPLATE_FEE: {
      return state
        .setIn(["template", "loading"], true)
        .setIn(["template", "data"], undefined);
    }
    case FETCH_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE: {
      return state
        .setIn(["template", "loading"], false)
        .setIn(["template", "data"], action.payload);
    }
    case FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE:
      return state
        .setIn(["template_list", "loading"], true)
        .setIn(["template", "loading"], true);
    case FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE: {
      let list = [];
      let total_page = 1;
      if (action.payload) {
        list = action.payload.data;
        total_page = action.payload.totalPage;
      }
      return state
        .setIn(["template_list", "loading"], false)
        .setIn(["template_list", "data"], list)
        .setIn(["template_list", "totalPage"], total_page)
        .setIn(["template", "loading"], false)
        .setIn(["template", "data"], list.length ? list[0] : undefined);
    }
    case FETCH_APARTMENT_SENT:
      return state.setIn(["apartmentToSend", "loading"], true);
    case FETCH_APARTMENT_SENT_COMPLETE: {
      let data = [];
      let totalPage = 1;
      let total_count = {
        total_apartment: 0,
        total_app: 0,
        total_email: 0,
        total_sms: 0,
      };

      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
        total_count = action.payload.total_count;
      }

      return state
        .setIn(["apartmentToSend", "loading"], false)
        .setIn(["apartmentToSend", "data"], data)
        .setIn(["apartmentToSend", "totalPage"], totalPage)
        .setIn(["apartmentToSend", "total_count"], total_count);
    }
    default:
      return state;
  }
}

export default notificationAddReducer;
