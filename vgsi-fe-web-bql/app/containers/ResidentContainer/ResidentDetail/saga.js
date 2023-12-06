import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_APARTMENT,
  REMOVE_APARTMENT,
  FETCH_DETAIL_RESIDENT,
  FETCH_BUILDING_AREA,
  UPDATE_DETAIL,
  ADDING_APARTMENT,
  VERIFY_PHONE_OTP,
  CHANGE_PHONE,
} from "./constants";
import {
  fetchApartmentCompleteAction,
  removeApartmentCompleteAction,
  fetchDetailResidentCompleteAction,
  fetchBuildingAreaCompleteAction,
  updateDetailCompleteAction,
  addApartmentCompleteAction,
  verifyPhoneOtpCompleteAction,
  changePhoneCompleteAction,
} from "./actions";
import { selectBuildingCluster } from "../../../redux/selectors";
import { notificationBar, parseTree } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchApartments(action) {
  try {
    let res = yield window.connection.fetchApartmentByResident(action.payload);
    if (res.success) {
      yield put(fetchApartmentCompleteAction(res.data.items));
    } else {
      yield put(fetchApartmentCompleteAction());
    }
  } catch (error) {
    yield put(fetchApartmentCompleteAction());
  }
}

function* _removeApartment(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.removeMemberOfApartment(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Remove property successful.");
      } else {
        notificationBar("Loại bỏ bất động sản thành công.");
      }
      callback && callback();
    } else {
      yield put(removeApartmentCompleteAction());
    }
  } catch (error) {
    yield put(removeApartmentCompleteAction());
  }
}

function* _fetchDetail(action) {
  try {
    let res = yield window.connection.fetchDetailResident(action.payload);
    if (res.success) {
      yield put(fetchDetailResidentCompleteAction(res.data));
    } else {
      yield put(fetchDetailResidentCompleteAction());
    }
  } catch (error) {
    console.log(error);
    yield put(fetchDetailResidentCompleteAction());
  }
}

function* _fetchBuildingArea(action) {
  try {
    let res = yield window.connection.getBuildingArea({ pageSize: 20000 });
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
    const { callback, message, ...rest } = action.payload;
    let res = yield window.connection.updateResident(rest);
    if (res.success) {
      callback && callback();
      message && notificationBar(message);
      yield _fetchDetail(callback());
    } else {
      yield put(updateDetailCompleteAction());
    }
  } catch (error) {
    yield put(updateDetailCompleteAction());
  }
}

function* _addApartment(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.addMemberOfApartment(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Add property successful.");
      } else {
        notificationBar("Thêm bất động sản thành công.");
      }
      callback && callback();
    } else {
      yield put(addApartmentCompleteAction());
    }
  } catch (error) {
    yield put(addApartmentCompleteAction());
  }
}

function* _verifyPhoneOTP(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.verifyOTPChangePhone(rest);
    // const language = yield select(makeSelectLocale());
    if (res.success) {
      // if (language === "en") {
      //   notificationBar("Change phone number successfully.");
      // } else {
      //   notificationBar("Thay đổi số điện thoại thành công.");
      // }
      // notificationBar(res.message);
      callback && callback();
    } else {
      yield put(verifyPhoneOtpCompleteAction(false));
    }
  } catch (error) {
    yield put(verifyPhoneOtpCompleteAction(false));
  }
}

function* _changePhone(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.changePhoneResident(rest);
    // const language = yield select(makeSelectLocale());
    if (res.success) {
      // if (language === "en") {
      //   notificationBar("Change phone successful.");
      // } else {
      //   notificationBar("Thay đổi số điện thoại thành công.");
      // }
      callback && callback();
    } else {
      yield put(changePhoneCompleteAction());
    }
  } catch (error) {
    yield put(changePhoneCompleteAction());
  }
}

// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(FETCH_APARTMENT, _fetchApartments),
    takeLatest(REMOVE_APARTMENT, _removeApartment),
    takeLatest(FETCH_DETAIL_RESIDENT, _fetchDetail),
    takeLatest(FETCH_BUILDING_AREA, _fetchBuildingArea),
    takeLatest(UPDATE_DETAIL, _updateDetail),
    takeLatest(ADDING_APARTMENT, _addApartment),
    takeLatest(VERIFY_PHONE_OTP, _verifyPhoneOTP),
    takeLatest(CHANGE_PHONE, _changePhone),
  ]);
}
