import { fromJS } from "immutable";
import {
  CHANGE_TASK_STATUS_COMPLETE,
  CREATE_TASK_COMMENT_COMPLETE,
  DEFAULT_ACTION,
  FETCH_DETAIL_TASK,
  FETCH_DETAIL_TASK_COMPLETE,
  FETCH_TASK_COMMENTS,
  FETCH_TASK_COMMENTS_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  detail: {
    loading: false,
    data: [],
  },
  comments: [],
  activities: [],
  loadingComment: false,
});

function taskDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;

    case FETCH_DETAIL_TASK:
      return state.setIn(["detail", "loading"], true);
    case FETCH_DETAIL_TASK_COMPLETE:
      return state
        .setIn(["detail", "loading"], false)
        .setIn(
          ["detail", "data"],
          action.payload ? fromJS(action.payload) : -1
        );
    case FETCH_TASK_COMMENTS:
      return state.set("loadingComment", true);
    case FETCH_TASK_COMMENTS_COMPLETE:
      let comments = action.payload.items
        ? action.payload.items.filter((e) => e.type === 0)
        : [];
      const activities = action.payload.items
        ? action.payload.items.filter((e) => e.type === 1)
        : [];
      return state
        .set("loadingComment", false)
        .set("comments", fromJS(comments))
        .set("activities", fromJS(activities));
    case CHANGE_TASK_STATUS_COMPLETE:
      return state.setIn(["detail", "data", "status"], action.payload);
    case CREATE_TASK_COMMENT_COMPLETE:
      const data = state.get("comments").toJS();
      data.push(action.payload);
      return state.set("comments", fromJS(data));
    default:
      return state;
  }
}

export default taskDetailReducer;
