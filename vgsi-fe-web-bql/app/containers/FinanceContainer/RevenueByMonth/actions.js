import { DEFAULT_ACTION, FETCH_REVENUE_BY_MONTH_ACTION,  FETCH_REVENUE_BY_MONTH_COMPLETE_ACTION} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchRevenueByMonthAction(payload) {
  return {
    type: FETCH_REVENUE_BY_MONTH_ACTION,
    payload
  };
}

export function fetchRevenueByMonthCompleteAction(payload) {
  return {
    type: FETCH_REVENUE_BY_MONTH_COMPLETE_ACTION,
    payload
  };
}
