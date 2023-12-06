import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_DETAIL_COMBINE_CARD,
  DELETE_COMBINE_CARD,
  UPDATE_DETAIL,
  UPDATE_DETAIL_COMPLETE,
  CHANGE_COMBINE_CARD_STATUS,
  FETCH_APARTMENT,
  FETCH_MEMBER,
  CREATE_ACTIVE_CARD,
} from "./constants";
import {
  fetchDetailCombineCardCompleteAction,
  updateDetailCompleteAction,
  deleteCombineCardCompleteAction,
  changeCombineCardStatusCompleteAction,
  changeCombineCardStatusErrorAction,
  fetchApartmentCompleteAction,
  fetchMemberCompleteAction,
  createActiveCardComplete,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { CardStatuses } from "utils/constants";
import { store } from "app";
import { notification } from "antd";

function* _fetchDetail(action) {
  try {
    let res = yield window.connection.fetchDetailCombineCard(action.payload);
    if (res.success) {
      yield put(fetchDetailCombineCardCompleteAction(res.data));
    } else {
      yield put(fetchDetailCombineCardCompleteAction());
    }
  } catch (error) {
    yield put(fetchDetailCombineCardCompleteAction());
  }
}

function* _updateDetail(action) {
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.updateCombineCard(rest);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update merge card successful.");
      } else {
        notificationBar("Cập nhật thẻ hợp nhất thành công.");
      }
      callback && callback();
    } else {
      yield put(updateDetailCompleteAction());
    }
  } catch (error) {
    yield put(updateDetailCompleteAction());
  }
}

function* _deleteCombineCard(action) {
  try {
    const { id, callback } = action.payload;
    let res = yield window.connection.deleteCombineCard({ id });
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete merge card successful.");
      } else {
        notificationBar("Xóa thẻ hợp nhất thành công.");
      }
      callback && callback();
    } else {
      yield put(deleteCombineCardCompleteAction());
    }
  } catch (error) {
    yield put(deleteCombineCardCompleteAction());
  }
}

function* _changeCombineCardStatus(action) {
  try {
    let res = yield window.connection.changeCombineCardStatus(action.payload);
    if (res.success) {
      if (action.payload.status === CardStatuses.cancel) {
        const language = store.getState().getIn(["language", "locale"]);

        notification.success({
          message:
            language === "vi"
              ? "Huỷ thẻ thành công"
              : "Cancel card successfully",
          placement: "bottomRight",
        });
      }
      yield put(changeCombineCardStatusCompleteAction(action.payload.status));
    } else {
      yield put(changeCombineCardStatusErrorAction());
    }
  } catch (error) {
    yield put(changeCombineCardStatusErrorAction());
  }
}
function* _fetchAllApartment(action) {
  try {
    let res = yield window.connection.fetchAllApartment({
      ...action.payload,
      pageSize: 20,
    });
    if (res.success) {
      yield put(fetchApartmentCompleteAction(res.data.items));
    } else {
      yield put(fetchApartmentCompleteAction());
    }
  } catch (error) {
    yield put(fetchApartmentCompleteAction());
  }
}
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
function* _createActiveCard(action) {
  try {
    const { callback } = action.payload;
    let res = yield window.connection.createActiveCard(action.payload);
    if (res.success) {
      notificationBar("Kích hoạt thẻ thành công.");
      yield put(createActiveCardComplete({ status: true, data: res.data }));
      callback && callback();
    } else {
      yield put(createActiveCardComplete());
    }
  } catch (error) {
    yield put(createActiveCardComplete());
  }
}
// Individual exports for testing
export default function* loginSaga() {
  yield all([
    takeLatest(DELETE_COMBINE_CARD, _deleteCombineCard),
    takeLatest(FETCH_DETAIL_COMBINE_CARD, _fetchDetail),
    takeLatest(UPDATE_DETAIL, _updateDetail),
    takeLatest(CHANGE_COMBINE_CARD_STATUS, _changeCombineCardStatus),
    takeLatest(FETCH_APARTMENT, _fetchAllApartment),
    takeLatest(FETCH_MEMBER, _fetchMembers),
    takeLatest(CREATE_ACTIVE_CARD, _createActiveCard),
  ]);
}
