import { all, put, takeLatest } from "redux-saga/effects";
import {
  CHANGE_TASK_STATUS,
  CREATE_TASK_COMMENT,
  FETCH_DETAIL_TASK,
  FETCH_TASK_COMMENTS,
} from "./constants";
import {
  fetchDetailTaskCompleteAction,
  fetchTaskCommentsCompleteAction,
  changeTaskStatusCompleteAction,
  changeTaskStatusErrorAction,
  createTaskCommentErrorAction,
  createTaskCommentCompleteAction,
  fetchTaskCommentsAction,
} from "./actions";
import { notification } from "antd";
import { JobStatuses } from "utils/constants";
import { store } from "app";

export default function* taskDetailSaga() {
  yield all([takeLatest(FETCH_DETAIL_TASK, _fetchTaskDetail)]);
  yield all([takeLatest(FETCH_TASK_COMMENTS, _fetchTaskComments)]);
  yield all([takeLatest(CHANGE_TASK_STATUS, _changeTaskStatus)]);
  yield all([takeLatest(CREATE_TASK_COMMENT, _createTaskComment)]);
}

function* _fetchTaskDetail(action) {
  try {
    let res = yield window.connection.fetchDetailTask(action.payload);
    if (res.success) {
      yield put(fetchDetailTaskCompleteAction(res.data));
    } else {
      yield put(fetchDetailTaskCompleteAction());
    }
  } catch (error) {
    yield put(fetchDetailTaskCompleteAction());
  }
}
function* _fetchTaskComments(action) {
  try {
    let res = yield window.connection.fetchTaskComments(action.payload);
    if (res.success) {
      yield put(fetchTaskCommentsCompleteAction(res.data));
    } else {
      yield put(fetchTaskCommentsCompleteAction([]));
    }
  } catch (error) {
    yield put(fetchTaskCommentsCompleteAction([]));
  }
}

function* _changeTaskStatus(action) {
  try {
    let res = yield window.connection.changeTaskStatus(action.payload);
    if (res.success) {
      if (action.payload.status === JobStatuses.cancel) {
        const language = store.getState().getIn(["language", "locale"]);

        notification.success({
          message:
            language === "vi"
              ? "Huỷ công việc thành công"
              : "Cancel task successfully",
          placement: "bottomRight",
        });
      }
      yield put(changeTaskStatusCompleteAction(action.payload.status));
    } else {
      yield put(changeTaskStatusErrorAction());
    }
  } catch (error) {
    yield put(changeTaskStatusErrorAction());
  }
}

function* _createTaskComment(action) {
  try {
    let res = yield window.connection.createTaskComment(action.payload);
    if (res.success) {
      yield put(fetchTaskCommentsAction({ job_id: action.payload.job_id }));
    } else {
      yield put(createTaskCommentErrorAction());
    }
  } catch (error) {
    yield put(createTaskCommentErrorAction());
  }
}
