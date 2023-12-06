/*
 *
 * AccountChangePassword reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  CHANGE_PASSWORD,
  CHANGE_PASSWORD_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  changing: false,
  success: false,
});

function accountChangePasswordReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case CHANGE_PASSWORD:
      return state.set("changing", true).set("success", false);
    case CHANGE_PASSWORD_COMPLETE:
      return state
        .set("changing", false)
        .set("success", action.payload || false);
    default:
      return state;
  }
}

export default accountChangePasswordReducer;
