import { take, all, put, select, takeLatest, call } from 'redux-saga/effects';
import { FETCH_APARTMENT_FEE_REMINDER, FETCH_ANNOUNCEMENT_TEMPLATE_FEE, FETCH_CATEGORY, CREATE_NOTIFICATION_FEE_REMINDER, FETCH_BUILDING_AREA_ACTION, FETCH_APARTMENT_SENT, FETCH_DETAIL_ANNOUNCEMENT, UPDATE_NOTIFICATION_ACTION } from './constants';
import { fetchApartmentFeeReminderComplete, fetchAnnouncementFeeTemplateComplete, fetchCategoryComplete, createNotificationUpdateReminderComplete, fetchBuildingAreaCompleteAction, fetchApartmentSentComplete, fetchDetailAnnouncementComplete, fetchApartmentSent, updateNotificationCompleteAction } from './actions';
import { notificationBar } from '../../../utils';


function* _fetchApartmentFeeReminder(action) {
  try {
    let res = yield window.connection.fetchApartmentFeeReminder(action.payload);
    if (res.success) {
      yield put(fetchApartmentFeeReminderComplete({
        data: res.data.items,
        totalPage: res.data.pagination.totalCount,
        total_count: res.data.total_count
      }));
    } else {
      yield put(fetchApartmentFeeReminderComplete());
    }
  } catch (error) {
    yield put(fetchApartmentFeeReminderComplete())
  }
}


function* _fetchAnnouncementTemplateFee(action) {
  try {
    let res = yield window.connection.fetchAnnouncementTemplateFee(action.payload);
    if (res.success) {
      yield put(fetchAnnouncementFeeTemplateComplete(res.data));
    } else {
      yield put(fetchAnnouncementFeeTemplateComplete());
    }
  } catch (error) {
    yield put(fetchAnnouncementFeeTemplateComplete())
  }
}

function* _fetchCategory(action) {
  try {
    let res = yield window.connection.fetchNotificationCategory({ type: 1, pageSize: 20000 });
    if (res.success) {
      yield put(fetchCategoryComplete(res.data.items));
    } else {
      yield put(fetchCategoryComplete());
    }
  } catch (error) {
    yield put(fetchCategoryComplete())
  }
}

function* _createNotificationUpdateReminder(action) {
  try {
    const { message, ...rest } = action.payload
    let res = yield window.connection.createNotification(rest)
    if (res.success) {
      notificationBar(message)
      yield put(createNotificationUpdateReminderComplete(true));
    } else {
      yield put(createNotificationUpdateReminderComplete());
    }
  } catch (error) {
    yield put(createNotificationUpdateReminderComplete())
  }
}


function* _fetchBuildingArea(action) {
  try {
    let res = yield window.connection.getBuildingArea({ pageSize: 100000, page: 1 })
    if (res.success) {
      yield put(fetchBuildingAreaCompleteAction(res.data.items))
    } else {
      yield put(fetchBuildingAreaCompleteAction())
    }
  } catch (error) {
    yield put(fetchBuildingAreaCompleteAction())
  }
}

function* _fetchApartmentSent(action) {
  try {
    let res = yield window.connection.fetchAnnouncementApartmentSent(action.payload);
    if (res.success) {
      yield put(fetchApartmentSentComplete({
        data: res.data.items,
        totalPage: res.data.pagination.totalCount,
        total_count: res.data.total_count
      }));
    } else {
      yield put(fetchApartmentSentComplete());
    }
  } catch (error) {
    yield put(fetchApartmentSentComplete())
  }
}

function* _fetchNotificationDetail(action) {
  try {
    let res = yield window.connection.fetchDetailNotification({ id: action.payload });
    if (res.success) {
      yield put(fetchDetailAnnouncementComplete(res.data));
      yield put(fetchApartmentSent({
        page: 1,
        pageSize: 20,
        building_area_ids: res.data.building_area_ids
      }))
    } else {
      yield put(fetchDetailAnnouncementComplete());
    }
  } catch (error) {
    yield put(fetchDetailAnnouncementComplete())
  }
}

function* _updateNotification(action) {
  try {
    const { message, ...rest } = action.payload
    let res = yield window.connection.updateNotification(rest)
    if (res.success) {
      notificationBar(message)
      yield put(updateNotificationCompleteAction(true))
    } else {
      yield put(updateNotificationCompleteAction())
    }
  } catch (error) {
    yield put(updateNotificationCompleteAction())
  }
}


// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_APARTMENT_FEE_REMINDER, _fetchApartmentFeeReminder),
    takeLatest(FETCH_ANNOUNCEMENT_TEMPLATE_FEE, _fetchAnnouncementTemplateFee),
    takeLatest(FETCH_CATEGORY, _fetchCategory),
    takeLatest(CREATE_NOTIFICATION_FEE_REMINDER, _createNotificationUpdateReminder),
    takeLatest(FETCH_BUILDING_AREA_ACTION, _fetchBuildingArea),
    takeLatest(FETCH_APARTMENT_SENT, _fetchApartmentSent),
    takeLatest(FETCH_DETAIL_ANNOUNCEMENT, _fetchNotificationDetail),
    takeLatest(UPDATE_NOTIFICATION_ACTION, _updateNotification),
  ])
}
