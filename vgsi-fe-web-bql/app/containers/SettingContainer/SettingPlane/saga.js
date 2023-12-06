import { CREATE_AREA, DELETE_AREA, GET_BUILDING_AREA, UPDATE_AREA } from "./constants";

import { take, all, put, select, takeLatest, call } from 'redux-saga/effects';
import {
  createAreaCompleteAction,
  getBuildingAreaCompleteAction,
  getBuildingAreaAction,
  updateAreaCompleteAction,
  deleteAreaCompleteAction
} from "./actions";
import { notification } from "antd";

function* _createArea(action) {
  try {
    const { callback, titleSuccess, ...rest } = action.payload
    let res = yield window.connection.createBuildingAreaCreate(rest)
    if (res.success) {
      notification['success']({
        placement: 'bottomRight',
        duration: 3,
        onClose: () => { },
        message: titleSuccess,
      });
      callback && callback()
      yield put(getBuildingAreaAction())
      yield put(createAreaCompleteAction())
    } else {
      yield put(createAreaCompleteAction())
    }
  } catch (error) {
    yield put(createAreaCompleteAction())
  }
}
function* _updateBuildingArea(action) {
  try {
    const { callback, titleSuccess, ...rest } = action.payload
    let res = yield window.connection.updateBuildingAreaCreate(rest)
    if (res.success) {
      notification['success']({
        placement: 'bottomRight',
        duration: 3,
        onClose: () => { },
        message: titleSuccess,
      });
      callback && callback()
      yield put(getBuildingAreaAction())
      yield put(updateAreaCompleteAction())
    } else {
      yield put(updateAreaCompleteAction())
    }
  } catch (error) {
    yield put(updateAreaCompleteAction())
  }
}

function* _deleteBuildingArea(action) {
  try {
    const { id, message } = action.payload
    let res = yield window.connection.deleteBuildingAreaCreate({ id })
    console.log(res)
    if (res.success) {
      notification['success']({
        placement: 'bottomRight',
        duration: 3,
        onClose: () => { },
        message: message
      });
      yield put(getBuildingAreaAction())
      yield put(deleteAreaCompleteAction())
    } else {
      yield put(deleteAreaCompleteAction())
    }
  } catch (error) {
    yield put(deleteAreaCompleteAction())
  }
}

function* _getBuildingArea(action) {
  try {
    let res = yield window.connection.getBuildingArea({ pageSize: 10000 })
    if (res.success) {
      yield put(getBuildingAreaCompleteAction(res.data.items))
    } else {
      yield put(getBuildingAreaCompleteAction([]))
    }
  } catch (error) {
    yield put(getBuildingAreaCompleteAction([]))
  }
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(CREATE_AREA, _createArea),
    takeLatest(GET_BUILDING_AREA, _getBuildingArea),
    takeLatest(UPDATE_AREA, _updateBuildingArea),
    takeLatest(DELETE_AREA, _deleteBuildingArea),
  ])
}
