import { take, all, put, select, takeLatest, call } from "redux-saga/effects";

import {
  DEFAULT_ACTION,
  FETCH_TICKET_DETAIL,
  FETCH_EXTERNAL_MESSAGES,
  FETCH_INTERNAL_MESSAGES,
  UPDATE_TICKET_STATUS,
  FETCH_MANAGERMENT_GROUPS,
  SEND_EXTERNAL_MESSAGE,
  SEND_INTERNAL_MESSAGE,
  FETCH_AUTH_GROUP,
  ADD_MANAGERMENT_GROUPS,
  REMOVE_MANAGERMENT_GROUPS,
  FETCH_CATEGORY,
} from "./constants";

import { notificationBar } from "../../../utils";

import {
  fetchTicketDetailCompleteAction,
  fetchExternalMessagesCompleteAction,
  fetchInternalMessagesCompleteAction,
  updateTicketStatusCompleteAction,
  fetchManagerGroupsCompleteAction,
  sendExternalMessageCompleteAction,
  sendInternalMessageCompleteAction,
  fetchAuthGroupCompleteAction,
  addManagerGroupsCompleteAction,
  removeManagerGroupsCompleteAction,
  fetchCategoryCompleteAction,
} from "./actions";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

function* _fetchTicketDetail(action) {
  try {
    let res = yield window.connection.fetchTicketDetail(action.payload);
    if (res.success && res.statusCode == 200) {
      yield put(
        fetchTicketDetailCompleteAction({ ...res.data, permission: true })
      );
    } else if (res.success && res.statusCode == 402) {
      yield put(fetchTicketDetailCompleteAction({ permission: false }));
    } else {
      yield put(fetchTicketDetailCompleteAction());
    }
  } catch (error) {
    console.log(error);
    yield put(fetchTicketDetailCompleteAction());
  }
}
function* _fetchCategory(action) {
  try {
    let res = yield window.connection.fetchCategoryTicket({
      ...action.payload,
      pageSize: 2000,
    });
    console.log("res12316666", res);
    if (res.success) {
      yield put(fetchCategoryCompleteAction(res.data.items));
    } else {
      yield put(fetchCategoryCompleteAction());
    }
  } catch (error) {
    yield put(fetchCategoryCompleteAction());
  }
}
function* _fetchExternalMessages(action) {
  try {
    let res = yield window.connection.fetchExternalMessages(action.payload);
    if (res.success) {
      yield put(
        fetchExternalMessagesCompleteAction({
          data: res.data.items,
          totalPage: res.data.pagination ? res.data.pagination.totalCount : 1,
        })
      );
    } else {
      yield put(fetchExternalMessagesCompleteAction());
    }
  } catch (error) {
    console.log(error);
    yield put(fetchExternalMessagesCompleteAction());
  }
}

function* _fetchInternalMessages(action) {
  try {
    let res = yield window.connection.fetchInternalMessages(action.payload);
    if (res.success) {
      yield put(
        fetchInternalMessagesCompleteAction({
          data: res.data.items,
          totalPage: res.data.pagination ? res.data.pagination.totalCount : 1,
        })
      );
    } else {
      yield put(fetchInternalMessagesCompleteAction());
    }
  } catch (error) {
    console.log(error);
    yield put(fetchInternalMessagesCompleteAction());
  }
}

function* _updateTicketStatus(action) {
  const language = yield select(makeSelectLocale());
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.updateTicketStatus(rest);
    if (res.success) {
      if (language === "en") {
        notificationBar("Update status successfully.");
      } else {
        notificationBar("Chuyển trạng thái thành công.");
      }
      callback && callback();
    } else {
      yield put(updateTicketStatusCompleteAction());
    }
  } catch (error) {
    console.log(error);
    yield put(updateTicketStatusCompleteAction());
  }
}

function* _fetchManagermentGroups(action) {
  try {
    let res = yield window.connection.fetchManagerGroups(action.payload);
    if (res.success) {
      yield put(
        fetchManagerGroupsCompleteAction({
          data: res.data.items,
          totalPage: res.data.pagination ? res.data.pagination.totalCount : 1,
        })
      );
    } else {
      yield put(fetchManagerGroupsCompleteAction());
    }
  } catch (error) {
    console.log(error);
    yield put(fetchManagerGroupsCompleteAction());
  }
}

function* _sendExternalMessage(action) {
  try {
    let res = yield window.connection.sendExternalMessage(action.payload);
    if (res.success) {
      yield put(sendExternalMessageCompleteAction(res.data));
    } else {
      yield put(sendExternalMessageCompleteAction());
    }
  } catch (error) {
    console.log(error);
    yield put(sendExternalMessageCompleteAction());
  }
}

function* _sendInternalMessage(action) {
  try {
    let res = yield window.connection.sendInternalMessage(action.payload);
    if (res.success) {
      yield put(sendInternalMessageCompleteAction(res.data));
    } else {
      yield put(sendInternalMessageCompleteAction());
    }
  } catch (error) {
    console.log(error);
    yield put(sendInternalMessageCompleteAction());
  }
}

function* _fetchAuthGroup(action) {
  try {
    let res = yield window.connection.getGroupAuth();
    if (res.success) {
      yield put(fetchAuthGroupCompleteAction(res.data));
    } else {
      yield put(fetchAuthGroupCompleteAction());
    }
  } catch (error) {
    yield put(fetchAuthGroupCompleteAction());
  }
  // yield put(loginSuccess())
}

function* _addProcessGroup(action) {
  const language = yield select(makeSelectLocale());
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.addProcssGroups(rest);
    if (res.success) {
      if (language === "en") {
        notificationBar("Add in charge team successfully.");
      } else {
        notificationBar("Thêm nhóm xử lý thành công");
      }
      yield put(addManagerGroupsCompleteAction());
      callback && callback();
    } else {
      yield put(addManagerGroupsCompleteAction());
    }
  } catch (error) {
    console.log(error);
    yield put(addManagerGroupsCompleteAction());
  }
}

function* _removeProcessGroup(action) {
  const language = yield select(makeSelectLocale());
  try {
    const { callback, ...rest } = action.payload;
    let res = yield window.connection.removeProcssGroups(rest);
    if (res.success) {
      if (language === "en") {
        notificationBar("Delete team in charge of processing successfully.");
      } else {
        notificationBar("Xóa nhóm xử lý thành công!");
      }

      yield put(removeManagerGroupsCompleteAction());
      callback && callback();
    } else {
      yield put(removeManagerGroupsCompleteAction());
    }
  } catch (error) {
    console.log(error);
    yield put(removeManagerGroupsCompleteAction());
  }
}

export default function* ticketDetailSaga() {
  yield all([
    takeLatest(FETCH_TICKET_DETAIL, _fetchTicketDetail),
    takeLatest(FETCH_EXTERNAL_MESSAGES, _fetchExternalMessages),
    takeLatest(FETCH_INTERNAL_MESSAGES, _fetchInternalMessages),
    takeLatest(UPDATE_TICKET_STATUS, _updateTicketStatus),
    takeLatest(FETCH_MANAGERMENT_GROUPS, _fetchManagermentGroups),
    takeLatest(SEND_EXTERNAL_MESSAGE, _sendExternalMessage),
    takeLatest(SEND_INTERNAL_MESSAGE, _sendInternalMessage),
    takeLatest(FETCH_AUTH_GROUP, _fetchAuthGroup),
    takeLatest(ADD_MANAGERMENT_GROUPS, _addProcessGroup),
    takeLatest(REMOVE_MANAGERMENT_GROUPS, _removeProcessGroup),
    takeLatest(FETCH_CATEGORY, _fetchCategory),
  ]);
}
