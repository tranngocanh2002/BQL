/*
 *
 * RolesCreate reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_PERMISSION,
  FETCH_ALL_PERMISSION_COMPLETE,
  CREATE_GROUP_AUTH,
  CREATE_GROUP_AUTH_COMPLETE,
  FETCH_DETAIL,
  FETCH_DETAIL_COMPLETE,
  CREATE_AUTH_ITEM_WEB,
  CREATE_AUTH_ITEM_WEB_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  allPermission: {
    loading: true,
    lst: [],
  },
  groupDetail: {
    loading: false,
    data: undefined,
  },
  isCreating: false,
  isCreateSuccess: false,
  isCreateAuthItem: false,
});

function rolesCreateReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_PERMISSION:
      return state.setIn(["allPermission", "loading"], true);
    case FETCH_ALL_PERMISSION_COMPLETE:
      return state
        .setIn(["allPermission", "loading"], false)
        .setIn(["allPermission", "lst"], action.payload);
    case FETCH_DETAIL:
      return state.setIn(["groupDetail", "loading"], true);
    case FETCH_DETAIL_COMPLETE: {
      return state
        .setIn(["groupDetail", "loading"], false)
        .setIn(["groupDetail", "data"], action.payload ? action.payload : -1);
    }
    case CREATE_GROUP_AUTH: {
      return state.set("isCreating", true).set("isCreateSuccess", false);
    }
    case CREATE_GROUP_AUTH_COMPLETE: {
      let isCreateSuccess = action.payload.success || false;
      return state
        .set("isCreating", false)
        .set("isCreateSuccess", isCreateSuccess);
    }
    case CREATE_AUTH_ITEM_WEB: {
      return state.set("isCreateAuthItem", true);
    }
    case CREATE_AUTH_ITEM_WEB_COMPLETE: {
      return state.set("isCreateAuthItem", false);
    }
    default:
      return state;
  }
}

export default rolesCreateReducer;
