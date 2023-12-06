/*
 *
 * maintainAdd reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  CREATE_EQUIPMENT,
  CREATE_EQUIPMENT_COMPLETE,
  FETCH_DETAIL_MAINTAIN,
  FETCH_DETAIL_MAINTAIN_COMPLETE,
  UPDATE_EQUIPMENT,
  UPDATE_EQUIPMENT_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  creating: false,
  success: false,
  updating: false,
  updateSuccess: false,
  detail: {
    loading: false,
    data: undefined,
  },
});

function maintainAddReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case CREATE_EQUIPMENT:
      return state.set("creating", true);
    case CREATE_EQUIPMENT_COMPLETE:
      return state
        .set("creating", false)
        .set("success", action.payload || false);
    case UPDATE_EQUIPMENT:
      return state.set("updating", true);
    case UPDATE_EQUIPMENT_COMPLETE:
      return state
        .set("updating", false)
        .set("updateSuccess", action.payload || false);
    case FETCH_DETAIL_MAINTAIN:
      return state.setIn(["detail", "loading"], true);
    case FETCH_DETAIL_MAINTAIN_COMPLETE:
      return state
        .setIn(["detail", "loading"], false)
        .setIn(
          ["detail", "data"],
          action.payload ? fromJS(action.payload) : -1
        );

    default:
      return state;
  }
}

export default maintainAddReducer;
