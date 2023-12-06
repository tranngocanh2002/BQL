import { take, all, put, select, takeLatest, call } from "redux-saga/effects";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import {
  createEquipmentCompleteAction,
  updateEquipmentCompleteAction,
} from "./actions";
import { notificationBar } from "utils";
import { CREATE_EQUIPMENT, UPDATE_EQUIPMENT } from "./constants";

function* _createEquipment(action) {
  try {
    let res = yield window.connection.createEquipment(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Add new equipment successful.");
      } else {
        notificationBar("Thêm mới thiết bị thành công.");
      }
      yield put(createEquipmentCompleteAction(true));
    } else {
      yield put(createEquipmentCompleteAction());
    }
  } catch (error) {
    yield put(createEquipmentCompleteAction());
  }
}

function* _updateEquipment(action) {
  try {
    let res = yield window.connection.updateEquipment(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update equipment successful.");
      } else {
        notificationBar("Cập nhật thiết bị thành công.");
      }
      yield put(updateEquipmentCompleteAction(true));
    } else {
      yield put(updateEquipmentCompleteAction());
    }
  } catch (error) {
    yield put(updateEquipmentCompleteAction());
  }
}

// function* _fetchSupplierDetail(action) {
//   try {
//     let res = yield window.connection.fetchContractorDetail(action.payload);
//     if (res.success) {
//       yield put(fetchSupplierDetailCompleteAction(res.data));
//     } else {
//       yield put(fetchSupplierDetailCompleteAction());
//     }
//   } catch (error) {
//     yield put(fetchSupplierDetailCompleteAction());
//   }
// }
export default function* rootSaga() {
  yield all([
    takeLatest(CREATE_EQUIPMENT, _createEquipment),
    takeLatest(UPDATE_EQUIPMENT, _updateEquipment),
  ]);
}
