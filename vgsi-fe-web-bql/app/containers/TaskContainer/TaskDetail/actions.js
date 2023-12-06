import {
  CHANGE_TASK_STATUS,
  CHANGE_TASK_STATUS_COMPLETE,
  CHANGE_TASK_STATUS_ERROR,
  CREATE_TASK_COMMENT,
  CREATE_TASK_COMMENT_COMPLETE,
  CREATE_TASK_COMMENT_ERROR,
  DEFAULT_ACTION,
  FETCH_DETAIL_TASK,
  FETCH_DETAIL_TASK_COMPLETE,
  FETCH_TASK_COMMENTS,
  FETCH_TASK_COMMENTS_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchDetailTaskAction(payload) {
  return {
    type: FETCH_DETAIL_TASK,
    payload,
  };
}

export function fetchDetailTaskCompleteAction(payload) {
  return {
    type: FETCH_DETAIL_TASK_COMPLETE,
    payload,
  };
}

export function fetchTaskCommentsAction(payload) {
  return {
    type: FETCH_TASK_COMMENTS,
    payload,
  };
}

export function fetchTaskCommentsCompleteAction(payload) {
  return {
    type: FETCH_TASK_COMMENTS_COMPLETE,
    payload,
  };
}

export function changeTaskStatusAction(payload) {
  return {
    type: CHANGE_TASK_STATUS,
    payload,
  };
}

export function changeTaskStatusCompleteAction(payload) {
  return {
    type: CHANGE_TASK_STATUS_COMPLETE,
    payload,
  };
}

export function changeTaskStatusErrorAction() {
  return {
    type: CHANGE_TASK_STATUS_ERROR,
  };
}

export function createTaskCommentAction(payload) {
  return {
    type: CREATE_TASK_COMMENT,
    payload,
  };
}

export function createTaskCommentCompleteAction(payload) {
  return {
    type: CREATE_TASK_COMMENT_COMPLETE,
    payload,
  };
}

export function createTaskCommentErrorAction(payload) {
  return {
    type: CREATE_TASK_COMMENT_ERROR,
    payload,
  };
}
