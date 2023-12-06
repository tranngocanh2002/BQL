/*
 *
 * Register reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  GET_CAPTCHA,
  GET_CAPTCHA_COMPLETE,
  FORGOT_PASS,
  FORGOT_PASS_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  captcha: {
    loading: false,
    data: undefined,
  },
  loading: false,
  success: false,
});

function registerReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case GET_CAPTCHA: {
      return state.setIn(["captcha", "loading"], true);
    }
    case GET_CAPTCHA_COMPLETE: {
      return state
        .setIn(["captcha", "loading"], false)
        .setIn(["captcha", "data"], action.payload);
    }
    case FORGOT_PASS:
      return state.set("loading", true);
    case FORGOT_PASS_COMPLETE:
      return state.set("loading", false).set("success", action.payload);
    default:
      return state;
  }
}

export default registerReducer;
