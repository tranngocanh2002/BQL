/*
 *
 * Login reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION } from "./constants";

export const initialState = fromJS({
  isLogining: false,
  success: false,
  countCallFall: 0,
  confirmCode: "",
  captchaImage: undefined,
  captcha: {
    loading: false,
    data: undefined,
  },
});

function loginReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;

    default:
      return state;
  }
}

export default loginReducer;
