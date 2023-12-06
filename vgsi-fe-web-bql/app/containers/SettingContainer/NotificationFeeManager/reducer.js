/*
 *
 * NotificationFeeManager reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  CREATE_ANNOUNCEMENT_TEMPLATE_FEE,
  CREATE_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  UPDATE_ANNOUNCEMENT_TEMPLATE_FEE,
  UPDATE_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  SHOW_CHOOSE_TEMPLATE_LIST,
  CHOOSE_CREATE_TEMPLATE,
  DELETE_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  DELETE_ANNOUNCEMENT_TEMPLATE_FEE,
} from "./constants";

export const initialState = fromJS({
  template_list: {
    loading: false,
    data: [],
    totalPage: 1,
  },
  template: {
    loading: false,
    data: undefined,
  },
  showChooseTemplate: true,
  chooseCreateTemplate: false,
  creating: false,
  createSuccess: false,
  updating: false,
  updateSuccess: false,
  loadingDelete: false,
});

function notificationFeeManagerReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case SHOW_CHOOSE_TEMPLATE_LIST:
      return state.set("showChooseTemplate", action.payload);
    case CHOOSE_CREATE_TEMPLATE:
      return state.set("chooseCreateTemplate", action.payload);
    case CREATE_ANNOUNCEMENT_TEMPLATE_FEE:
      return state.set("creating", true).set("creatSuccess", false);
    case CREATE_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE:
      return state
        .set("creating", false)
        .set("createSuccess", action.payload || false);
    case UPDATE_ANNOUNCEMENT_TEMPLATE_FEE:
      return state.set("updating", true).set("updateSuccess", false);
    case UPDATE_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE:
      return state
        .set("updating", false)
        .set("updateSuccess", action.payload || false);
    case FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE:
      return state
        .setIn(["template_list", "loading"], true)
        .setIn(["template", "loading"], true);
    case FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE:
      let list = [];
      let total_page = 1;
      if (!!action.payload) {
        list = action.payload.data;
        total_page = action.payload.totalPage;
      }
      return state
        .setIn(["template_list", "loading"], false)
        .setIn(["template_list", "data"], list)
        .setIn(["template_list", "totalPage"], total_page)
        .setIn(["template", "loading"], false)
        .setIn(["template", "data"], list.length ? list[0] : undefined);
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
    case DELETE_ANNOUNCEMENT_TEMPLATE_FEE: {
      return state.set("loadingDelete", true);
    }
    case DELETE_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE: {
      return state.set("loadingDelete", false);
    }
    default:
      return state;
  }
}

export default notificationFeeManagerReducer;
