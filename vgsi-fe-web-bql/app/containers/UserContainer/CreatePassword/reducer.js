/*
 *
 * CreatePassword reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  CHECK_TOKEN_COMPLETE,
  CREATE_PASSWORD,
  CREATE_PASSWORD_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  firstLoading: true,
  errorChecking: false,
  loading: false,
  successCreate: false,
});

function createPasswordReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case CHECK_TOKEN_COMPLETE: {
      return state
        .set("firstLoading", false)
        .set("errorChecking", action.payload);
    }
    case CREATE_PASSWORD: {
      return state.set("loading", true);
    }
    case CREATE_PASSWORD_COMPLETE: {
      return state.set("loading", false).set("successCreate", action.payload);
    }
    default:
      return state;
  }
}

export default createPasswordReducer;
