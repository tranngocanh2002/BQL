/*
 *
 * Dashboard reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_COUNT_REPORT,
  FETCH_COUNT_REPORT_COMPLETE,
  FETCH_ANNOUNCEMENT,
  FETCH_ANNOUNCEMENT_COMPLETE,
  FETCH_REQUEST,
  FETCH_REQUEST_COMPLETE,
  FETCH_FINANCE,
  FETCH_FINANCE_COMPLETE,
  BOOKING_REVENUE,
  BOOKING_REVENUE_COMPLETE,
  BOOKING_LIST_REVENUE_COMPLETE,
  BOOKING_LIST_REVENUE,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_RESIDENT_COMPLETE,
  FETCH_RESIDENT,
  FETCH_MAINTENANCE,
  FETCH_MAINTENANCE_COMPLETE,
} from "./constants";
import moment from "moment";

export const initialState = fromJS({
  countAll: {
    loading: false,
    data: undefined,
  },
  announcement: {
    loading: false,
    data: [],
  },
  request: {
    loading: false,
    data: [],
    month: moment().unix(),
  },
  finance: {
    loading: false,
    data: [],
    from_month: moment().subtract(5, "months").unix(),
    to_month: moment().unix(),
  },
  maintenance: {
    loading: false,
    data: [],
    from_month: moment().subtract(5, "months").startOf("month").unix(),
    to_month: moment().endOf("month").unix(),
  },
  apartment: {
    loading: false,
    data: undefined,
  },
  resident: {
    loading: false,
    data: undefined,
  },
  booking: {
    loading: false,
    data: [],
    month: moment().unix(),
  },
  booking_list: {
    loading: false,
    data: [],
    month: moment().unix(),
  },
});

function dashboardReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_COUNT_REPORT:
      return state.setIn(["countAll", "loading"], true);
    case FETCH_COUNT_REPORT_COMPLETE:
      return state
        .setIn(["countAll", "loading"], false)
        .setIn(["countAll", "data"], action.payload);
    case FETCH_ANNOUNCEMENT:
      return state.setIn(["announcement", "loading"], true);
    case FETCH_ANNOUNCEMENT_COMPLETE:
      return state
        .setIn(["announcement", "loading"], false)
        .setIn(["announcement", "data"], action.payload);
    case FETCH_REQUEST: {
      const { month } = action.payload || {};
      return state
        .setIn(["request", "loading"], true)
        .setIn(
          ["request", "month"],
          month || state.get("request").get("month")
        );
    }
    case FETCH_REQUEST_COMPLETE:
      return state
        .setIn(["request", "loading"], false)
        .setIn(["request", "data"], action.payload, {});
    case FETCH_FINANCE: {
      const { from_month, to_month } = action.payload || {};
      return state
        .setIn(["finance", "loading"], true)
        .setIn(
          ["finance", "from_month"],
          from_month || state.get("finance").get("from_month")
        )
        .setIn(
          ["finance", "to_month"],
          to_month || state.get("finance").get("to_month")
        );
    }
    case FETCH_FINANCE_COMPLETE:
      return state
        .setIn(["finance", "loading"], false)
        .setIn(["finance", "data"], action.payload, {});
    case FETCH_MAINTENANCE: {
      const { from_month, to_month } = action.payload || {};
      return state
        .setIn(["maintenance", "loading"], true)
        .setIn(
          ["maintenance", "from_month"],
          from_month || state.get("maintenance").get("from_month")
        )
        .setIn(
          ["maintenance", "to_month"],
          to_month || state.get("maintenance").get("to_month")
        );
    }
    case FETCH_MAINTENANCE_COMPLETE:
      return state
        .setIn(["maintenance", "loading"], false)
        .setIn(["maintenance", "data"], action.payload, {});
    case BOOKING_REVENUE: {
      const { month } = action.payload || {};
      return state
        .setIn(["booking", "loading"], true)
        .setIn(
          ["booking", "month"],
          month || state.get("booking").get("month")
        );
    }
    case BOOKING_REVENUE_COMPLETE:
      return state
        .setIn(["booking", "loading"], false)
        .setIn(["booking", "data"], action.payload, {});
    case BOOKING_LIST_REVENUE: {
      const { month } = action.payload || {};
      return state
        .setIn(["booking_list", "loading"], true)
        .setIn(
          ["booking_list", "month"],
          month || state.get("booking_list").get("month")
        );
    }
    case BOOKING_LIST_REVENUE_COMPLETE:
      return state
        .setIn(["booking_list", "loading"], false)
        .setIn(["booking_list", "data"], action.payload, {});
    case FETCH_APARTMENT: {
      return state.setIn(["apartment", "loading"], true);
    }
    case FETCH_APARTMENT_COMPLETE:
      return state
        .setIn(["apartment", "loading"], false)
        .setIn(["apartment", "data"], action.payload, {});
    case FETCH_RESIDENT: {
      return state.setIn(["resident", "loading"], true);
    }
    case FETCH_RESIDENT_COMPLETE:
      return state
        .setIn(["resident", "loading"], false)
        .setIn(["resident", "data"], action.payload, {});
    default:
      return state;
  }
}

export default dashboardReducer;
