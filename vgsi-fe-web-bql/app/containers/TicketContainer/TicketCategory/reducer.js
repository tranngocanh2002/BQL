/*
 *
 * TicketCategory reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_AUTH_GROUP, FETCH_AUTH_GROUP_COMPLETE, CREATE_CATEGORY, CREATE_CATEGORY_COMPLETE, FETCH_CATEGORY_COMPLETE, FETCH_CATEGORY, DELETE_CATEGORY, DELETE_CATEGORY_COMPLETE, UPDATE_CATEGORY, UPDATE_CATEGORY_COMPLETE } from "./constants";

export const initialState = fromJS({
  authGroup: {
    loading: false,
    lst: []
  },
  creating: false,
  updating: false,
  loading: false,
  totalPage: 1,
  data: [],
  deleting: false,
});

function ticketCategoryReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case CREATE_CATEGORY:
      return state.set('creating', true)
    case CREATE_CATEGORY_COMPLETE:
      return state.set('creating', false)
    case UPDATE_CATEGORY:
      return state.set('updating', true)
    case UPDATE_CATEGORY_COMPLETE:
      return state.set('updating', false)
    case FETCH_AUTH_GROUP:
      return state.setIn(['authGroup', 'loading'], true);
    case FETCH_AUTH_GROUP_COMPLETE:
      return state.setIn(['authGroup', 'loading'], false)
        .setIn(['authGroup', 'lst'], fromJS(action.payload || []))
    case FETCH_CATEGORY:
      return state.set('loading', true)
    case FETCH_CATEGORY_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
      }

      return state.set('loading', false).
        set('data', fromJS(data)).set('totalPage', totalPage).set('deleting', false).set('updating', false)
    }
    case DELETE_CATEGORY:
      return state.set('deleting', true)
    case DELETE_CATEGORY_COMPLETE:
      return state.set('deleting', false)
    default:
      return state;
  }
}

export default ticketCategoryReducer;
