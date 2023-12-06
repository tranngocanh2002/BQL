import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_MEMBER,
  REMOVE_MEMBER,
  FETCH_DETAIL_APARTMENT,
  FETCH_BUILDING_AREA,
  UPDATE_DETAIL,
  ADDING_MEMBER,
  UPDATING_MEMBER,
  FETCH_ALL_APARTMENT_TYPE,
  FETCH_ALL_RESIDENT_BY_PHONE,
} from "./constants";
import {
  fetchMemberCompleteAction,
  removeMemberCompleteAction,
  fetchDetailApartmentCompleteAction,
  fetchBuildingAreaCompleteAction,
  updateDetailCompleteAction,
  addMemberCompleteAction,
  updateMemberAction,
  updateMemberCompleteAction,
  fetchAllApartmentTypeComplete,
  fetchAllResidentByPhoneCompleteAction,
} from "./actions";
import { selectBuildingCluster } from "../../../redux/selectors";
import { notificationBar, notificationBar2, parseTree } from "../../../utils";
import { notification } from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchMembers(action) {
  try {
    let res = yield window.connection.fetchMemberOfApartment(action.payload);
    if (res.success) {
      yield put(fetchMemberCompleteAction(res.data.items));
    } else {
      yield put(fetchMemberCompleteAction());
    }
  } catch (error) {
    yield put(fetchMemberCompleteAction());
  }
}
function* _removeMember(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.removeMemberOfApartment(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete member property successful.");
      } else {
        notificationBar("Xóa thành viên bất động sản thành công.");
      }
      callback && callback();
    } else {
      yield put(removeMemberCompleteAction());
    }
  } catch (error) {
    yield put(removeMemberCompleteAction());
  }
}
function* _fetchDetail(action) {
  try {
    let res = yield window.connection.fetchDetailApartment(action.payload);
    if (res.success) {
      yield put(fetchDetailApartmentCompleteAction(res.data));
    } else {
      yield put(fetchDetailApartmentCompleteAction());
    }
  } catch (error) {
    yield put(fetchDetailApartmentCompleteAction());
  }
}

function* _fetchBuildingArea(action) {
  try {
    let res = yield window.connection.getBuildingArea({ pageSize: 10000 });
    if (res.success) {
      let root = yield select(selectBuildingCluster());
      yield put(
        fetchBuildingAreaCompleteAction(
          parseTree(
            root.data,
            res.data.items.map((iii) => ({
              children: [],
              key: `${iii.id}`,
              ...iii,
            }))
          )
        )
      );
    } else {
      yield put(fetchBuildingAreaCompleteAction([]));
    }
  } catch (error) {
    yield put(fetchBuildingAreaCompleteAction([]));
  }
}

function* _updateDetail(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.updateApartment(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update property successful.");
      } else {
        notificationBar("Cập nhật bất động sản thành công.");
      }
      callback && callback();
    } else {
      yield put(updateDetailCompleteAction());
    }
  } catch (error) {
    yield put(updateDetailCompleteAction());
  }
}

function* _addMember(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.addMemberOfApartment(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Add member to property successful.");
      } else {
        notificationBar("Thêm thành viên vào bất động sản thành công.");
      }
      yield put(addMemberCompleteAction(true));
      callback && callback();
    } else {
      // if (language === "en" && res.statusCode === 501) {
      //   notificationBar2("The property does not have a householder");
      // }
      yield put(addMemberCompleteAction());
    }
  } catch (error) {
    yield put(addMemberCompleteAction());
  }
}
function* _updateMember(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.updateTypeMemberOfApartment(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update successful.");
      } else {
        notificationBar("Cập nhật thành công.");
      }
      yield put(updateMemberCompleteAction());
      callback && callback();
    } else {
      yield put(updateMemberCompleteAction());
    }
  } catch (error) {
    yield put(updateMemberCompleteAction());
  }
}
function* _fetchApartmentType(action) {
  try {
    let res = yield window.connection.fetchAllApartmentType({});
    if (res.success) {
      yield put(fetchAllApartmentTypeComplete(res.data));
    } else {
      yield put(fetchAllApartmentTypeComplete());
    }
  } catch (error) {
    yield put(fetchAllApartmentTypeComplete());
  }
}

function* _fetchResidentByPhone(action) {
  try {
    let res = yield window.connection.fetchResidentByPhone({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(
        fetchAllResidentByPhoneCompleteAction({
          // data: res.data.items.map(mm => ({ id: mm.apartment_map_resident_user_id, phone: mm.phone, name: mm.first_name })),
          data: res.data.items,
          totalPage: res.data.pagination.totalCount,
        })
      );
    } else {
      yield put(fetchAllResidentByPhoneCompleteAction());
    }
  } catch (error) {
    yield put(fetchAllResidentByPhoneCompleteAction());
  }
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(FETCH_MEMBER, _fetchMembers),
    takeLatest(REMOVE_MEMBER, _removeMember),
    takeLatest(FETCH_DETAIL_APARTMENT, _fetchDetail),
    takeLatest(FETCH_BUILDING_AREA, _fetchBuildingArea),
    takeLatest(UPDATE_DETAIL, _updateDetail),
    takeLatest(ADDING_MEMBER, _addMember),
    takeLatest(UPDATING_MEMBER, _updateMember),
    takeLatest(FETCH_ALL_APARTMENT_TYPE, _fetchApartmentType),
    takeLatest(FETCH_ALL_RESIDENT_BY_PHONE, _fetchResidentByPhone),
  ]);
}
