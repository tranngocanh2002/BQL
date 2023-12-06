import { fromJS } from "immutable";
import moment from "moment";
import {
  DEFAULT_ACTION,
  FETCH_REVENUE_BY_MONTH_ACTION,
  FETCH_REVENUE_BY_MONTH_COMPLETE_ACTION,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  data: [],
});

function revenueByMonthReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_REVENUE_BY_MONTH_ACTION:
      return state.set("loading", true);
    case FETCH_REVENUE_BY_MONTH_COMPLETE_ACTION:
      return state.set("loading", false).set("data", action.payload);
    default:
      return state;
  }
}

export default revenueByMonthReducer;
