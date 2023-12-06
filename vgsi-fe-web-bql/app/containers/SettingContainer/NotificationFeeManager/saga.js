import { all, put, select, takeLatest } from "redux-saga/effects";
import {
  FETCH_ANNOUNCEMENT_TEMPLATE_FEE,
  FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE,
  CREATE_ANNOUNCEMENT_TEMPLATE_FEE,
  UPDATE_ANNOUNCEMENT_TEMPLATE_FEE,
  DELETE_ANNOUNCEMENT_TEMPLATE_FEE,
} from "./constants";
import {
  fetchAnnouncementFeeTemplateComplete,
  fetchAllAnnouncementFeeTemplateComplete,
  createAnnouncementFeeTemplateComplete,
  updateAnnouncementFeeTemplateComplete,
  deleteAnnouncementFeeTemplateComplete,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

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

function* _createAnnouncementTemplateFee(action) {
  try {
    let res = yield window.connection.createAnnouncementTemplateFee(
      action.payload
    );
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Create new template notice successful.");
      } else {
        notificationBar("Tạo mới mẫu thông báo thành công.");
      }
      yield put(createAnnouncementFeeTemplateComplete(true));
    } else {
      yield put(createAnnouncementFeeTemplateComplete());
    }
  } catch (error) {
    yield put(createAnnouncementFeeTemplateComplete());
  }
}

function* _updateAnnouncementTemplateFee(action) {
  try {
    let res = yield window.connection.updateAnnouncementTemplateFee(
      action.payload
    );
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update template notice successful.");
      } else {
        notificationBar("Cập nhật mẫu thông báo thành công.");
      }
      yield put(updateAnnouncementFeeTemplateComplete(true));
    } else {
      yield put(updateAnnouncementFeeTemplateComplete());
    }
  } catch (error) {
    yield put(updateAnnouncementFeeTemplateComplete());
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

function* _deleteAnnouncementTemplateFee(action) {
  const { callback, ...rest } = action.payload;
  try {
    let res = yield window.connection.deleteAnnouncementTemplateFee(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete template notice successful.");
      } else {
        notificationBar("Xóa mẫu thông báo thành công.");
      }
      callback && callback();
      yield put(deleteAnnouncementFeeTemplateComplete(res.data));
    } else {
      yield put(deleteAnnouncementFeeTemplateComplete());
    }
  } catch (error) {
    yield put(deleteAnnouncementFeeTemplateComplete());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(
      FETCH_ALL_ANNOUNCEMENT_TEMPLATE_FEE,
      _fetchAllAnnouncementTemplateFee
    ),
    takeLatest(FETCH_ANNOUNCEMENT_TEMPLATE_FEE, _fetchAnnouncementTemplateFee),
    takeLatest(
      CREATE_ANNOUNCEMENT_TEMPLATE_FEE,
      _createAnnouncementTemplateFee
    ),
    takeLatest(
      UPDATE_ANNOUNCEMENT_TEMPLATE_FEE,
      _updateAnnouncementTemplateFee
    ),
    takeLatest(
      DELETE_ANNOUNCEMENT_TEMPLATE_FEE,
      _deleteAnnouncementTemplateFee
    ),
  ]);
}
