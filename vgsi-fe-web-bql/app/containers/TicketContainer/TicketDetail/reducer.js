/*
 *
 * TicketDetail reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_TICKET_DETAIL,
  FETCH_TICKET_DETAIL_COMPLETE,
  FETCH_EXTERNAL_MESSAGES,
  FETCH_EXTERNAL_MESSAGES_COMPLETE,
  FETCH_INTERNAL_MESSAGES,
  FETCH_INTERNAL_MESSAGES_COMPLETE,
  UPDATE_TICKET_STATUS,
  UPDATE_TICKET_STATUS_COMPLETE,
  FETCH_MANAGERMENT_GROUPS,
  FETCH_MANAGERMENT_GROUPS_COMPLETE,
  SEND_EXTERNAL_MESSAGE,
  SEND_EXTERNAL_MESSAGE_COMPLETE,
  SEND_INTERNAL_MESSAGE,
  SEND_INTERNAL_MESSAGE_COMPLETE,
  FETCH_AUTH_GROUP,
  FETCH_AUTH_GROUP_COMPLETE,
  ADD_MANAGERMENT_GROUPS,
  ADD_MANAGERMENT_GROUPS_COMPLETE,
  REMOVE_MANAGERMENT_GROUPS,
  REMOVE_MANAGERMENT_GROUPS_COMPLETE,
  FETCH_CATEGORY_COMPLETE,
  FETCH_CATEGORY,
} from "./constants";

export const initialState = fromJS({
  managerment_groups: {
    loading: false,
    adding: false,
    removing: false,
    data: [],
    totalPage: 0,
  },
  file_uploading: false,
  detail: {
    loading: false,
    data: undefined,
  },
  categories: {
    loading: false,
    lst: [],
  },
  ticket_updating: false,
  external_sending: false,
  internal_sending: false,
  internal_messages: {
    loading: false,
    data: [],
    totalPage: 0,
  },
  external_messages: {
    loading: false,
    data: [],
    totalPage: 0,
  },
  authGroup: {
    loading: false,
    lst: [],
  },
});

function ticketDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_TICKET_DETAIL:
      return state.setIn(["detail", "loading"], true);
    case FETCH_TICKET_DETAIL_COMPLETE:
      return state
        .setIn(["detail", "loading"], false)
        .setIn(
          ["detail", "data"],
          action.payload ? fromJS(action.payload) : -1
        );
    case FETCH_EXTERNAL_MESSAGES:
      return state.setIn(["external_messages", "loading"], true);
    case FETCH_EXTERNAL_MESSAGES_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }
      return state
        .setIn(["external_messages", "data"], fromJS(data))
        .setIn(["external_messages", "totalPage"], totalPage)
        .setIn(["external_messages", "loading"], false);
    }
    case FETCH_INTERNAL_MESSAGES:
      return state.setIn(["internal_messages", "loading"], true);
    case FETCH_INTERNAL_MESSAGES_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }
      return state
        .setIn(["internal_messages", "data"], fromJS(data))
        .setIn(["internal_messages", "totalPage"], totalPage)
        .setIn(["internal_messages", "loading"], false);
    }
    case FETCH_MANAGERMENT_GROUPS:
      return state.setIn(["managerment_groups", "loading"], true);
    case FETCH_MANAGERMENT_GROUPS_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }
      return state
        .setIn(["managerment_groups", "data"], fromJS(data))
        .setIn(["managerment_groups", "totalPage"], totalPage)
        .setIn(["managerment_groups", "loading"], false);
    }
    case ADD_MANAGERMENT_GROUPS:
      return state.setIn(["managerment_groups", "adding"], true);
    case ADD_MANAGERMENT_GROUPS_COMPLETE:
      return state.setIn(["managerment_groups", "adding"], false);
    case REMOVE_MANAGERMENT_GROUPS:
      return state.setIn(["managerment_groups", "removing"], true);
    case REMOVE_MANAGERMENT_GROUPS_COMPLETE:
      return state.setIn(["managerment_groups", "removing"], false);
    case UPDATE_TICKET_STATUS:
      return state.set("ticket_updating", true);
    case UPDATE_TICKET_STATUS_COMPLETE:
      return state.set("ticket_updating", false);
    case SEND_EXTERNAL_MESSAGE:
      return state.set("external_sending", true);
    case SEND_EXTERNAL_MESSAGE_COMPLETE: {
      if (action.payload) {
        let data = [
          ...state.get("external_messages").get("data").toJS(),
          action.payload,
        ];
        return state
          .setIn(["external_messages", "data"], fromJS(data))
          .set("external_sending", false);
      }
      return state.set("external_sending", false);
    }
    case SEND_INTERNAL_MESSAGE:
      return state.set("internal_sending", true);
    case SEND_INTERNAL_MESSAGE_COMPLETE: {
      if (action.payload) {
        let data = [
          ...state.get("internal_messages").get("data").toJS(),
          action.payload,
        ];
        return state
          .setIn(["internal_messages", "data"], fromJS(data))
          .set("internal_sending", false);
      }
      return state.set("internal_sending", false);
    }

    case FETCH_AUTH_GROUP:
      return state.setIn(["authGroup", "loading"], true);
    case FETCH_AUTH_GROUP_COMPLETE:
      return state
        .setIn(["authGroup", "loading"], false)
        .setIn(["authGroup", "lst"], fromJS(action.payload || []));
    case FETCH_CATEGORY:
      return state.setIn(["categories", "loading"], true);
    case FETCH_CATEGORY_COMPLETE: {
      return state
        .setIn(["categories", "loading"], false)
        .setIn(
          ["categories", "lst"],
          action.payload ? fromJS(action.payload) : -1
        );
    }
    default:
      return state;
  }
}

export default ticketDetailReducer;
