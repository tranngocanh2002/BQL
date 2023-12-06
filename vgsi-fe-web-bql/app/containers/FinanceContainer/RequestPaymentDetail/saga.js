import { all, put, select, takeLatest } from "redux-saga/effects";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { notificationBar } from "utils";
import { deleteRequestComplete } from "./actions";
import { DELETE_REQUEST } from "./constants";

function* _deleteRequest(action) {
  try {
    let res = yield window.connection.deletePaymentRequest(action.payload);
    let language = yield select(makeSelectLocale());

    action.payload.callback && action.payload.callback();
    if (res.success) {
      if (language === "en") {
        notificationBar("Reject request payment successfully.");
      } else {
        notificationBar("Từ chối yêu cầu thanh toán thành công.");
      }
      yield put(deleteRequestComplete());
    } else {
      yield put(deleteRequestComplete());
    }
  } catch (error) {
    yield put(deleteRequestComplete());
  }
}

// Individual exports for testing
export default function* billListSaga() {
  yield all([takeLatest(DELETE_REQUEST, _deleteRequest)]);
}
