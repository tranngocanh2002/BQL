/*
 *
 * NotificationDetail reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_APARTMENT_FEE_REMINDER,
  FETCH_APARTMENT_FEE_REMINDER_COMPLETE,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE,
  CREATE_NOTIFICATION_FEE_REMINDER,
  CREATE_NOTIFICATION_FEE_REMINDER_COMPLETE,
  FETCH_CATEGORY,
  FETCH_CATEGORY_COMPLETE,
  FETCH_NOTIFICATION_DETAIL,
  FETCH_NOTIFICATION_DETAIL_COMPLETE,
  FETCH_APARTMENT_SENT,
  FETCH_APARTMENT_SENT_COMPLETE,
  FETCH_SURVEY_ANSWER_COMPLETE,
  FETCH_SURVEY_ANSWER,
  FETCH_REPORT_CHART,
  FETCH_REPORT_CHART_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  apartmentReminder: {
    loading: true,
    data: [],
    totalPage: 1,
    total_count: {
      total_apartment: 0,
      total_app: 0,
      total_email: 0,
      total_sms: 0,
    },
  },
  reportChart: {
    loading: true,
    data: {
      pie_chart: [],
      bar_chart: [],
    },
  },
  surveyAnswer: {
    loading: true,
    data: [],
    pagination: {
      totalCount: 20,
      pageCount: 1,
      currentPage: 1,
      pageSize: 20,
    },
  },
  template: {
    loading: true,
    data: undefined,
  },
  category: {
    loading: true,
    data: [],
  },
  loading: true,
  detail: undefined,
  sending: false,
  sentSuccess: false,
});

function notificationDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_NOTIFICATION_DETAIL: {
      return state.set("loading", true);
    }
    case FETCH_NOTIFICATION_DETAIL_COMPLETE: {
      return state.set("loading", false).set("detail", action.payload);
    }
    case FETCH_SURVEY_ANSWER:
      return state.setIn(["surveyAnswer", "loading"], true);
    case FETCH_SURVEY_ANSWER_COMPLETE: {
      let data = [];
      let pagination = {
        totalCount: 20,
        pageCount: 1,
        currentPage: 1,
        pageSize: 20,
      };
      if (action.payload) {
        data = action.payload.data;
        pagination = action.payload.pagination;
      }
      return state
        .setIn(["surveyAnswer", "loading"], false)
        .setIn(["surveyAnswer", "data"], data)
        .setIn(["surveyAnswer", "pagination"], pagination);
    }
    case FETCH_REPORT_CHART:
      return state.setIn(["reportChart", "loading"], true);
    case FETCH_REPORT_CHART_COMPLETE: {
      let pie_chart = [];
      let bar_chart = [];

      if (action.payload) {
        pie_chart = action.payload.data.pie_chart;
        bar_chart = action.payload.data.bar_chart;
      }
      return state
        .setIn(["reportChart", "loading"], false)
        .setIn(["reportChart", "data", "pie_chart"], pie_chart)
        .setIn(["reportChart", "data", "bar_chart"], bar_chart);
    }
    case FETCH_CATEGORY:
      return state.setIn(["category", "loading"], true);
    case FETCH_CATEGORY_COMPLETE:
      return state
        .setIn(["category", "loading"], false)
        .setIn(["category", "data"], action.payload || []);
    case CREATE_NOTIFICATION_FEE_REMINDER:
      return state.set("sending", true).set("sentSuccess", false);
    case CREATE_NOTIFICATION_FEE_REMINDER_COMPLETE:
      return state
        .set("sending", false)
        .set("sentSuccess", action.payload || false);
    case FETCH_ANNOUNCEMENT_TEMPLATE_FEE: {
      return state
        .setIn(["template", "loading"], true)
        .setIn(["template", "data"], undefined);
    }
    case FETCH_ANNOUNCEMENT_TEMPLATE_FEE_COMPLETE: {
      return state
        .setIn(["template", "loading"], false)
        .setIn(["template", "data"], action.payload);
    }
    case FETCH_APARTMENT_FEE_REMINDER:
    case FETCH_APARTMENT_SENT:
      return state.setIn(["apartmentReminder", "loading"], true);
    case FETCH_APARTMENT_FEE_REMINDER_COMPLETE:
    case FETCH_APARTMENT_SENT_COMPLETE: {
      let data = [];
      let totalPage = 1;
      let total_count = {
        total_apartment: 0,
        total_app: 0,
        total_email: 0,
        total_sms: 0,
      };

      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
        total_count = action.payload.total_count;
      }

      return state
        .setIn(["apartmentReminder", "loading"], false)
        .setIn(["apartmentReminder", "data"], data)
        .setIn(["apartmentReminder", "totalPage"], totalPage)
        .setIn(["apartmentReminder", "total_count"], total_count);
    }
    default:
      return state;
  }
}

export default notificationDetailReducer;
