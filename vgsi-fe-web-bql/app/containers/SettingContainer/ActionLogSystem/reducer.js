/*
 *
 * ActionLogSystem reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ACTION_CONTROLER,
  FETCH_ACTION_CONTROLER_COMPLETE,
  FETCH_USER_MANAGEMENT,
  FETCH_USER_MANAGEMENT_COMPLETE,
  FETCH_LOGS,
  FETCH_LOGS_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  controllers: {
    loading: false,
    data: {},
  },
  userManagers: {
    loading: false,
    data: [],
  },
  logs: {
    loading: false,
    totalPage: 1,
    data: [],
  },
});

function actionLogSystemReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_LOGS:
      return state.setIn(["logs", "loading"], true);
    case FETCH_LOGS_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }

      return state
        .setIn(["logs", "loading"], false)
        .setIn(["logs", "data"], data)
        .setIn(["logs", "totalPage"], totalPage);
    }
    case FETCH_ACTION_CONTROLER:
      return state.setIn(["controllers", "loading"], true);
    case FETCH_ACTION_CONTROLER_COMPLETE:
      return state
        .setIn(["controllers", "loading"], false)
        .setIn(["controllers", "data"], action.payload || {});
    case FETCH_USER_MANAGEMENT:
      return state.setIn(["userManagers", "loading"], true);
    case FETCH_USER_MANAGEMENT_COMPLETE:
      return state
        .setIn(["userManagers", "loading"], false)
        .setIn(["userManagers", "data"], action.payload || []);
    default:
      return state;
  }
}

export default actionLogSystemReducer;
