/*
 *
 * MaintainDetail reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_DETAIL_EQUIPMENT,
  FETCH_DETAIL_EQUIPMENT_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  success: false,
  updating: false,
  updateSuccess: false,
  detail: {
    loading: false,
    data: undefined,
  },
});

function maintainDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_DETAIL_EQUIPMENT:
      return state.setIn(["detail", "loading"], true);
    case FETCH_DETAIL_EQUIPMENT_COMPLETE:
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

export default maintainDetailReducer;
