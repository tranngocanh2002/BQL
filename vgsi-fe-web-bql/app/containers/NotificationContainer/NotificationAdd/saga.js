import { all, put, takeLatest } from "redux-saga/effects";
import {
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_CATEGORY,
  CREATE_NOTIFICATION_FEE_REMINDER,
  FETCH_BUILDING_AREA_ACTION,
  FETCH_APARTMENT_SENT,
  FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE,
} from "./constants";
import {
  fetchAnnouncementFeeTemplateComplete,
  fetchCategoryComplete,
  createNotificationAddReminderComplete,
  fetchBuildingAreaCompleteAction,
  fetchApartmentSentComplete,
  fetchAllAnnouncementFeeTemplateComplete,
} from "./actions";
import { notificationBar } from "../../../utils";

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

//XXX: 4.2 Create saga to call api create notification
function* _createNotificationAddReminder(action) {
  try {
    const { message, ...rest } = action.payload;
    let res = yield window.connection.createNotification(rest);
    if (res.success) {
      notificationBar(message);
      //XXX: 6 Saga put method to call the action creator to dispatch action to reducer for update state
      yield put(createNotificationAddReminderComplete(true));
    } else {
      yield put(createNotificationAddReminderComplete());
    }
  } catch (error) {
    yield put(createNotificationAddReminderComplete());
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
      page: 1,
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
    takeLatest(FETCH_ANNOUNCEMENT_TEMPLATE_FEE, _fetchAnnouncementTemplateFee),
    takeLatest(FETCH_CATEGORY, _fetchCategory),
    takeLatest(
      CREATE_NOTIFICATION_FEE_REMINDER, //XXX: 4.1 Saga takeLatest method to listen action dispatched
      _createNotificationAddReminder
    ),
    takeLatest(FETCH_BUILDING_AREA_ACTION, _fetchBuildingArea),
    takeLatest(FETCH_APARTMENT_SENT, _fetchApartmentSent),
    takeLatest(
      FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE,
      _fetchAllAnnouncementTemplateFee
    ),
  ]);
}
