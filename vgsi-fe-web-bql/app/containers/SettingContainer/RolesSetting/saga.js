import { take, all, put, select, takeLatest, call } from "redux-saga/effects";
import {
  FETCH_ALL_PERMISSION,
  CREATE_GROUP_AUTH,
  FETCH_DETAIL,
  CREATE_AUTH_ITEM_WEB,
} from "./constants";

import {
  fetchAllPermissionComplete,
  createGroupAuthComplete,
  fetchGroupAuthDetailComplete,
  createAuthItemWebComplete,
} from "./actions";
import { notificationBar } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchDetail(action) {
  try {
    let res = yield window.connection.getGroupAuthDetail(action.payload);
    if (res.success) {
      yield put(fetchGroupAuthDetailComplete(res.data));
    } else {
      yield put(fetchGroupAuthDetailComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(fetchGroupAuthDetailComplete());
  }
}
function* _fetchAllPermission(action) {
  try {
    let res = yield window.connection.getAllPermission();
    if (res.success) {
      let data = {};
      res.data.forEach((ele) => {
        if (!data[ele.tag]) data[ele.tag] = [];
        data[ele.tag].push(ele);
      });
      yield put(
        fetchAllPermissionComplete(
          Object.keys(data).map((key) => {
            return {
              name: key,
              data: data[key],
            };
          })
        )
      );
    } else {
      yield put(fetchAllPermissionComplete([]));
    }
  } catch (error) {
    console.log(error);
    yield put(fetchAllPermissionComplete([]));
  }
}

function* _createOrUpdateGroupAuth(action) {
  try {
    const { name, name_en, description, data_role, id, type } = action.payload;
    let res;
    const language = yield select(makeSelectLocale());
    if (id) {
      res = yield window.connection.updateGroupAuthDetail({
        name,
        name_en,
        description: description || "",
        data_role,
        id,
        type,
      });
    } else {
      res = yield window.connection.createGroupAuth({
        name,
        name_en,
        description: description || "",
        data_role,
        type,
      });
    }
    if (res.success) {
      if (id) {
        if (language === "en") {
          notificationBar("Update roles group successful.");
        } else {
          notificationBar("Cập nhật nhóm quyền thành công.");
        }
      } else {
        if (language === "en") {
          notificationBar("Create roles group successful.");
        } else {
          notificationBar("Tạo nhóm quyền thành công.");
        }
      }
      yield put(createGroupAuthComplete({ success: true }));
    } else {
      yield put(createGroupAuthComplete({}));
    }
  } catch (error) {
    console.log(error);
    yield put(createGroupAuthComplete({}));
  }
}

// bỏ khi đưa lên product

function* _createAuthItemWeb(action) {
  try {
    let res = yield window.connection.createAuthItemWeb(action.payload);
    const language = yield select(makeSelectLocale());
    if (res.success) {
      if (language === "en") {
        notificationBar("Update all roles group successful.");
      } else {
        notificationBar("Cập nhật tất cả các quyền thành công.");
      }
      yield put(createAuthItemWebComplete(true));
    } else {
      yield put(createAuthItemWebComplete());
    }
  } catch (error) {
    console.log(error);
    yield put(createAuthItemWebComplete());
  }
}

// Individual exports for testing
export default function* rolesCreateSaga() {
  yield all([
    takeLatest(CREATE_GROUP_AUTH, _createOrUpdateGroupAuth),
    takeLatest(FETCH_ALL_PERMISSION, _fetchAllPermission),
    takeLatest(FETCH_DETAIL, _fetchDetail),
    takeLatest(CREATE_AUTH_ITEM_WEB, _createAuthItemWeb),
  ]);
}
