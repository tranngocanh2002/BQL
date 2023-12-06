import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_APARTMENT_FEE_REMINDER,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_CATEGORY,
  CREATE_NOTIFICATION_FEE_REMINDER,
  FETCH_NOTIFICATION_DETAIL,
  FETCH_APARTMENT_SENT,
  FETCH_SURVEY_ANSWER,
  FETCH_REPORT_CHART,
} from "./constants";
import {
  fetchApartmentFeeReminderComplete,
  fetchAnnouncementFeeTemplateComplete,
  fetchCategoryComplete,
  createNotificationDetailReminderComplete,
  fetchNotificationDetailComplete,
  fetchApartmentFeeReminder,
  fetchApartmentSent,
  fetchApartmentSentComplete,
  fetchSurveyAnswerComplete,
  fetchSurveyAnswer,
  fetchReportChartComplete,
  fetchReportChart,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchApartmentFeeReminder(action) {
  try {
    let res = yield window.connection.fetchAnnouncementFeeApartmentSent({
      ...action.payload,
      pageSize: 200000,
    });
    if (res.success) {
      yield put(
        fetchApartmentFeeReminderComplete({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
          total_count: res.data.total_count,
        })
      );
    } else {
      yield put(fetchApartmentFeeReminderComplete());
    }
  } catch (error) {
    yield put(fetchApartmentFeeReminderComplete());
  }
}

function* _fetchSurveyAnswer(action) {
  try {
    let res = yield window.connection.fetchSurveyAnswer({
      ...action.payload,
      // pageSize: 200000,
      //TODO api is not return the right page size
      // pageSize: 10,
    });
    if (res.success) {
      yield put(
        fetchSurveyAnswerComplete({
          data: res.data.items,
          pagination: res.data.pagination,
        })
      );
    } else {
      yield put(fetchSurveyAnswerComplete());
    }
  } catch (error) {
    yield put(fetchSurveyAnswerComplete());
  }
}

function* _fetchReportChart(action) {
  try {
    let res = yield window.connection.fetchReportChart({
      id: action.payload,
    });
    if (res.success) {
      yield put(
        fetchReportChartComplete({
          data: res.data,
        })
      );
    } else {
      yield put(fetchReportChartComplete());
    }
  } catch (error) {
    yield put(fetchReportChartComplete());
  }
}

function* _fetchApartmentSent(action) {
  try {
    let res = yield window.connection.fetchAnnouncementApartmentSent({
      ...action.payload,
      pageSize: 200000,
    });
    if (res.success) {
      yield put(
        fetchApartmentSentComplete({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
          total_count: res.data.total_count,
        })
      );
    } else {
      yield put(fetchApartmentSentComplete());
    }
  } catch (error) {
    yield put(fetchApartmentSentComplete());
  }
}

function* _fetchAnnouncementTemplateFee(action) {
  try {
    let res = yield window.connection.fetchAnnouncementTemplateFee(
      action.payload
    );
    if (res.success) {
      yield put(fetchAnnouncementFeeTemplateComplete(res.data));
    } else {
      yield put(fetchAnnouncementFeeTemplateComplete());
    }
  } catch (error) {
    yield put(fetchAnnouncementFeeTemplateComplete());
  }
}

function* _fetchNotificationDetail(action) {
  try {
    let res = yield window.connection.fetchDetailNotification({
      id: action.payload,
    });
    if (res.success) {
      yield put(fetchNotificationDetailComplete(res.data));
      if (res.data.status == 0) {
        yield put(
          fetchApartmentSent({
            page: 1,
            pageSize: 10,
            building_area_ids: res.data.building_area_ids,
            targets: res.data.targets,
          })
        );
      } else if (res.data.status == 1 && res.data.is_survey == 1) {
        yield all([
          put(
            fetchApartmentFeeReminder({
              announcement_campaign_id: res.data.id,
              page: 1,
              pageSize: 10,
            })
          ),
          put(
            fetchSurveyAnswer({
              // resident_user_id: 4,
              // apartment_id: 2,
              // status: 1,
              announcement_campaign_id: res.data.id,
              page: 1,
              pageSize: 10,
            })
            // fetchReportChart({
            //   id: res.data.id,
            // })
          ),
        ]);
      } else {
        yield put(
          fetchApartmentFeeReminder({
            announcement_campaign_id: res.data.id,
            page: 1,
            pageSize: 10,
          })
        );
      }
    } else {
      yield put(fetchNotificationDetailComplete());
    }
  } catch (error) {
    yield put(fetchNotificationDetailComplete());
  }
}

function* _createNotificationDetailReminder(action) {
  try {
    let res = yield window.connection.createNotification(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Create fee notification successful.");
      } else {
        notificationBar("Tạo thông báo phí thành công.");
      }
      yield put(createNotificationDetailReminderComplete(true));
    } else {
      yield put(createNotificationDetailReminderComplete());
    }
  } catch (error) {
    yield put(createNotificationDetailReminderComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_APARTMENT_FEE_REMINDER, _fetchApartmentFeeReminder),
    takeLatest(FETCH_APARTMENT_SENT, _fetchApartmentSent),
    takeLatest(FETCH_ANNOUNCEMENT_TEMPLATE_FEE, _fetchAnnouncementTemplateFee),
    takeLatest(FETCH_NOTIFICATION_DETAIL, _fetchNotificationDetail),
    takeLatest(
      CREATE_NOTIFICATION_FEE_REMINDER,
      _createNotificationDetailReminder
    ),
    takeLatest(FETCH_SURVEY_ANSWER, _fetchSurveyAnswer),
    takeLatest(FETCH_REPORT_CHART, _fetchReportChart),
  ]);
}
