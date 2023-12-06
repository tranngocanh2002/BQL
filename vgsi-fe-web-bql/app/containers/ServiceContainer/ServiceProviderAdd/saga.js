import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { CREATE_PROVIDER } from "./constants";
import { createProviderCompleteAction } from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _createProvider(action) {
  try {
    let res = yield window.connection.createServiceProvider(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Create contractor successful.");
      } else {
        notificationBar("Tạo nhà cung cấp thành công.");
      }
      yield put(createProviderCompleteAction(true));
    } else {
      yield put(createProviderCompleteAction());
    }
  } catch (error) {
    console.log(`error`, error);
    yield put(createProviderCompleteAction());
  }
}

// Individual exports for testing
export default function* rootSaga() {
  yield all([takeLatest(CREATE_PROVIDER, _createProvider)]);
}
