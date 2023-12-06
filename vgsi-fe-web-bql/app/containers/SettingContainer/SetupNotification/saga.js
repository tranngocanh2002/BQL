import { FETCH_ALL_GROUP, DELETE_GROUP, FETCH_BUILDING_CLUSTER, FETCH_ALL_ROLES, UPDATE_SETTING } from "./constants";
import { take, all, put, select, takeLatest, call } from 'redux-saga/effects';
import { fetchBuildingClusterComplete, deleteGroupComplete, fetchAllGroup, fetchAllRolesComplete, updateSettingComplete, fetchAllRoles, fetchBuildingCluster } from "./actions";
import { notificationBar } from "../../../utils";

function* _fetchBuildingCluster(action) {
  try {
    let res = yield window.connection.getBuildingClusterDetail()
    if (res.success) {
      yield put(fetchBuildingClusterComplete(res.data))
    } else {
      yield put(fetchBuildingClusterComplete())
    }
  } catch (error) {
    console.log(error)
    yield put(fetchBuildingClusterComplete())
  }
}
function* _fetchAllRoles(action) {
  try {
    let res = yield window.connection.getGroupAuth()
    if (res.success) {
      yield put(fetchAllRolesComplete(res.data))
    } else {
      yield put(fetchAllRolesComplete())
    }
  } catch (error) {
    console.log(error)
    yield put(fetchAllRolesComplete())
  }
}
function* _updateSetting(action) {
  try {
    const { messagePrint, ...rest } = action.payload
    console.log(rest)
    let res = yield window.connection.updateBuildingCluster(rest)
    if (res.success) {
      yield put(fetchBuildingCluster())
      notificationBar(messagePrint)
    } else {
      yield put(updateSettingComplete())
    }
  } catch (error) {
    console.log(error)
    yield put(updateSettingComplete())
  }
}

// Individual exports for testing
export default function* rolesSaga() {
  yield all([
    takeLatest(FETCH_BUILDING_CLUSTER, _fetchBuildingCluster),
    takeLatest(FETCH_ALL_ROLES, _fetchAllRoles),
    takeLatest(UPDATE_SETTING, _updateSetting),
  ])
}
