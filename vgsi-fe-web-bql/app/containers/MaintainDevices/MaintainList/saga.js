import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import { notificationBar } from "utils";
import {
  deleteMaintainDevicesCompleteAction,
  fetchAllMaintainDevicesCompleteAction,
  fetchAllMaintainScheduleCompleteAction,
  updateMaintainDevicesCompleteAction,
} from "./actions";
import {
  DELETE_MAINTAIN_DEVICES,
  FETCH_ALL_MAINTAIN_DEVICES,
  FETCH_ALL_MAINTAIN_SCHEDULE,
  UPDATE_DETAIL,
  UPDATE_MAINTAIN_SCHEDULE,
} from "./constants";

function* _fetchMaintainDeviceList(action) {
  try {
    let res = yield window.connection.fetchMaintainList({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchAllMaintainDevicesCompleteAction({
          data: res.data.items,
          totalCount: res.data.pagination ? res.data.pagination.totalCount : 0,
          pageCount: res.data.pagination ? res.data.pagination.pageCount : 0,
        })
      );
    } else {
      yield put(fetchAllMaintainDevicesCompleteAction());
    }
  } catch (error) {
    yield put(fetchAllMaintainDevicesCompleteAction());
  }
}

function* _deleteEquipment(action) {
  try {
    const { id, callback } = action.payload;
    let res = yield window.connection.deleteEquipment({ id: id });
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete equipment successful.");
      } else {
        notificationBar("Xóa thiết bị thành công.");
      }
      callback && callback();
      yield put(deleteMaintainDevicesCompleteAction());
    } else {
      yield put(deleteMaintainDevicesCompleteAction());
    }
  } catch (error) {
    yield put(deleteMaintainDevicesCompleteAction());
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
      yield put(updateMaintainDevicesCompleteAction(true));
    } else {
      yield put(updateMaintainDevicesCompleteAction());
    }
  } catch (error) {
    yield put(updateMaintainDevicesCompleteAction());
  }
}
function* _fetchMaintainScheduleList(action) {
  try {
    let res = yield window.connection.fetchMaintainScheduleList({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchAllMaintainScheduleCompleteAction({
          data: res.data.items,
          totalCount: res.data.pagination ? res.data.pagination.totalCount : 0,
          pageCount: res.data.pagination ? res.data.pagination.pageCount : 0,
        })
      );
    } else {
      yield put(fetchAllMaintainScheduleCompleteAction());
    }
  } catch (error) {
    yield put(fetchAllMaintainScheduleCompleteAction());
  }
}
function* _updateSchedule(action) {
  try {
    let res = yield window.connection.createMaintainSchedule(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update maintenance date successful.");
      } else {
        notificationBar("Cập nhật ngày bảo trì thành công.");
      }
      yield put(updateMaintainDevicesCompleteAction(true));
    } else {
      yield put(updateMaintainDevicesCompleteAction());
    }
  } catch (error) {
    yield put(updateMaintainDevicesCompleteAction());
  }
}
// Individual exports for testing
export default function* rootSaga() {
  yield all([
    takeLatest(FETCH_ALL_MAINTAIN_DEVICES, _fetchMaintainDeviceList),
    takeLatest(DELETE_MAINTAIN_DEVICES, _deleteEquipment),
    takeLatest(UPDATE_DETAIL, _updateEquipment),
    takeLatest(FETCH_ALL_MAINTAIN_SCHEDULE, _fetchMaintainScheduleList),
    takeLatest(UPDATE_MAINTAIN_SCHEDULE, _updateSchedule),
  ]);
}
