import { all, put, select, takeLatest } from "redux-saga/effects";
import {
  FETCH_APARTMENT_FEE_REMINDER,
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_CATEGORY,
  CREATE_NOTIFICATION_FEE_REMINDER,
  FETCH_NOTIFICATION_TO_PRINT,
  FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_BUILDING_AREA_ACTION,
  FETCH_APARTMENT_SENT,
} from "./constants";
import {
  fetchApartmentFeeReminderComplete,
  fetchAnnouncementFeeTemplateComplete,
  fetchCategoryComplete,
  createNotificationFeeReminderComplete,
  fetchAllAnnouncementFeeTemplateComplete,
  fetchApartmentSentComplete,
  fetchBuildingAreaCompleteAction,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchApartmentFeeReminder(action) {
  try {
    let res = yield window.connection.fetchApartmentFeeReminder({
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
    let res = yield window.connection.fetchNotificationCategory({
      type: 1,
      pageSize: 20000,
    });
    if (res.success) {
      yield put(fetchCategoryComplete(res.data.items));
    } else {
      yield put(fetchCategoryComplete());
    }
  } catch (error) {
    yield put(fetchCategoryComplete());
  }
}

function* _createNotificationFeeReminder(action) {
  try {
    let res = yield window.connection.createNotification(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Create fee notification successful.");
      } else {
        notificationBar("Tạo thông báo phí thành công.");
      }
      yield put(createNotificationFeeReminderComplete(true));
    } else {
      yield put(createNotificationFeeReminderComplete());
    }
  } catch (error) {
    yield put(createNotificationFeeReminderComplete());
  }
}

function* _fetchNotificationToPrint(action) {
  try {
    let res = yield window.connection.fetchNotificationToPrint(action.payload);
    const printWindow = document.createElement("iframe");
    printWindow.style.position = "absolute";
    printWindow.style.top = "-5000px";
    printWindow.style.left = "-1000px";
    document.body.appendChild(printWindow);
    printWindow.onload = () => {
      console.log("onLoad");
    };

    const domDoc =
      printWindow.contentDocument || printWindow.contentWindow.document;
    domDoc.open();
    domDoc.write(res);
    domDoc.close();
    setTimeout(() => {
      printWindow.contentWindow.focus();
      printWindow.contentWindow.print();
      setTimeout(() => {
        printWindow.parentNode.removeChild(printWindow);
      }, 500);
    }, 500);
  } catch (error) {
    console.log(error);
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

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_APARTMENT_FEE_REMINDER, _fetchApartmentFeeReminder),
    takeLatest(
      FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE,
      _fetchAllAnnouncementTemplateFee
    ),
    takeLatest(FETCH_ANNOUNCEMENT_TEMPLATE_FEE, _fetchAnnouncementTemplateFee),
    takeLatest(FETCH_CATEGORY, _fetchCategory),
    takeLatest(
      CREATE_NOTIFICATION_FEE_REMINDER,
      _createNotificationFeeReminder
    ),
    takeLatest(FETCH_NOTIFICATION_TO_PRINT, _fetchNotificationToPrint),
    takeLatest(FETCH_BUILDING_AREA_ACTION, _fetchBuildingArea),
    takeLatest(FETCH_APARTMENT_SENT, _fetchApartmentSent),
  ]);
}
