/*
 *
 * ManagementClusterServiceContainer reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_DETAIL_SERVICE,
  FETCH_DETAIL_SERVICE_COMPLETE,
} from "./constants";
export const initialState = fromJS({
  loading: false,
  data: undefined,
});

function managementClusterServiceContainerReducer(
  state = initialState,
  action
) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_DETAIL_SERVICE:
      return state.set("loading", true);
    case FETCH_DETAIL_SERVICE_COMPLETE:
      return state.set("loading", false).set("data", action.payload);
    default:
      return state;
  }
}

export default managementClusterServiceContainerReducer;
