import { all, put, takeLatest } from "redux-saga/effects";
import {
  FETCH_APARTMENT_FEE_REMINDER,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_CATEGORY,
  CREATE_NOTIFICATION_FEE_REMINDER,
  FETCH_BUILDING_AREA_ACTION,
  FETCH_APARTMENT_SENT,
  FETCH_DETAIL_ANNOUNCEMENT,
  UPDATE_NOTIFICATION_ACTION,
  FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE,
} from "./constants";
import {
  fetchApartmentFeeReminderComplete,
  fetchAnnouncementFeeTemplateComplete,
  fetchCategoryComplete,
  createNotificationUpdateReminderComplete,
  fetchBuildingAreaCompleteAction,
  fetchApartmentSentComplete,
  fetchDetailAnnouncementComplete,
  fetchApartmentSent,
  updateNotificationCompleteAction,
  fetchApartmentFeeReminder,
  fetchAllAnnouncementFeeTemplateComplete,
} from "./actions";
import { notificationBar } from "../../../utils";

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

function* _fetchCategory(action) {
  try {
    let res = yield window.connection.fetchNotificationCategory(action.payload);
    if (res.success) {
      yield put(fetchCategoryComplete(res.data.items));
    } else {
      yield put(fetchCategoryComplete());
    }
  } catch (error) {
    yield put(fetchCategoryComplete());
  }
}

function* _createNotificationUpdateReminder(action) {
  try {
    const { message, ...rest } = action.payload;
    let res = yield window.connection.createNotification(rest);
    if (res.success) {
      notificationBar(message);
      yield put(createNotificationUpdateReminderComplete(true));
    } else {
      yield put(createNotificationUpdateReminderComplete());
    }
  } catch (error) {
    yield put(createNotificationUpdateReminderComplete());
  }
}

function* _fetchBuildingArea(action) {
  try {
    let res = yield window.connection.getBuildingArea({
      pageSize: 100000,
      page: 1,
    });
    if (res.success) {
      yield put(fetchBuildingAreaCompleteAction(res.data.items));
    } else {
      yield put(fetchBuildingAreaCompleteAction());
    }
  } catch (error) {
    yield put(fetchBuildingAreaCompleteAction());
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

function* _fetchNotificationDetail(action) {
  try {
    let res = yield window.connection.fetchDetailNotification({
      id: action.payload,
    });
    if (res.success) {
      yield put(fetchDetailAnnouncementComplete(res.data));
      if (res.data.status == 0) {
        yield put(
          fetchApartmentSent({
            pageSize: 200000,
            building_area_ids: res.data.building_area_ids,
            targets: res.data.targets,
          })
        );
      } else {
        yield put(
          fetchApartmentFeeReminder({
            announcement_campaign_id: res.data.id,
            pageSize: 200000,
          })
        );
      }
    } else {
      yield put(fetchDetailAnnouncementComplete());
    }
  } catch (error) {
    yield put(fetchDetailAnnouncementComplete());
  }
}

function* _updateNotification(action) {
  try {
    const { message, survey_deadline, id, ...rest } = action.payload;
    const notificationUpdate = {
      ...rest,
      id: id,
    };
    let res = yield window.connection.updateNotification(notificationUpdate);
    if (survey_deadline) {
      const surveyExtend = {
        id,
        survey_deadline,
      };
      let res2 = yield window.connection.extendSurveyDeadline(surveyExtend);
    }
    //TODO : Do i need to use all? All will throw error if one of the request fail
    // let res = yield all([
    //   window.connection.updateNotification(notificationUpdate),
    //   window.connection.extendSurveyDeadline(surveyExtend),
    // ]);
    if (res.success) {
      notificationBar(message);
      yield put(updateNotificationCompleteAction(true));
    } else {
      yield put(updateNotificationCompleteAction());
    }
  } catch (error) {
    yield put(updateNotificationCompleteAction());
  }
}

function* _fetchAllAnnouncementTemplateFee(action) {
  try {
    let res = yield window.connection.fetchAllAnnouncementTemplateFee(
      action.payload
    );
    if (res.success) {
      yield put(
        fetchAllAnnouncementFeeTemplateComplete({
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchAllAnnouncementFeeTemplateComplete());
    }
  } catch (error) {
    yield put(fetchAllAnnouncementFeeTemplateComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_APARTMENT_FEE_REMINDER, _fetchApartmentFeeReminder),
    takeLatest(FETCH_ANNOUNCEMENT_TEMPLATE_FEE, _fetchAnnouncementTemplateFee),
    takeLatest(
      FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE,
      _fetchAllAnnouncementTemplateFee
    ),
    takeLatest(FETCH_CATEGORY, _fetchCategory),
    takeLatest(
      CREATE_NOTIFICATION_FEE_REMINDER,
      _createNotificationUpdateReminder
    ),
    takeLatest(FETCH_BUILDING_AREA_ACTION, _fetchBuildingArea),
    takeLatest(FETCH_APARTMENT_SENT, _fetchApartmentSent),
    takeLatest(FETCH_DETAIL_ANNOUNCEMENT, _fetchNotificationDetail),
    takeLatest(UPDATE_NOTIFICATION_ACTION, _updateNotification),
  ]);
}
