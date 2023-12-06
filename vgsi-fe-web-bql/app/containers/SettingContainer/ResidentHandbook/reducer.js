/*
 *
 * ResidentHandbook reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_CATEGORY, FETCH_CATEGORY_COMPLETE, ADD_CATEGORY, ADD_CATEGORY_COMPLETE, EDIT_CATEGORY, EDIT_CATEGORY_COMPLETE, DELETE_CATEGORY, DELETE_CATEGORY_COMPLETE, ADD_HANDBOOK_ITEM, EDIT_HANDBOOK_ITEM, DELETE_HANDBOOK_ITEM, DELETE_HANDBOOK_ITEM_COMPLETE, ADD_HANDBOOK_ITEM_COMPLETE, EDIT_HANDBOOK_ITEM_COMPLETE, FETCH_HANDBOOK_ITEM, FETCH_HANDBOOK_ITEM_COMPLETE } from "./constants";

export const initialState = fromJS({
  category: {
    loading: false,
    adding: false,
    addSuccess: false,
    deleting: false,
    data: []
  },
  handbook: {
    adding: false,
    addSuccess: false,
    deleting: false,
    data: {}
  }
});

function residentHandbookReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case ADD_CATEGORY:
    case EDIT_CATEGORY: {
      return state.setIn(['category', 'adding'], true).setIn(['category', 'addSuccess'], false)
    }
    case DELETE_CATEGORY: {
      return state.setIn(['category', 'deleting'], true)
    }
    case DELETE_CATEGORY_COMPLETE: {
      return state.setIn(['category', 'deleting'], false)
    }
    case ADD_CATEGORY_COMPLETE:
    case EDIT_CATEGORY_COMPLETE: {
      return state.setIn(['category', 'adding'], false).setIn(['category', 'addSuccess'], action.payload || false)
    }
    case FETCH_CATEGORY: {
      return state.setIn(['category', 'loading'], true)
    }
    case FETCH_CATEGORY_COMPLETE: {
      return state.setIn(['category', 'loading'], false)
        .setIn(['category', 'data'], action.payload || [])
    }
    case ADD_HANDBOOK_ITEM:
    case EDIT_HANDBOOK_ITEM: {
      return state.setIn(['handbook', 'adding'], true).setIn(['handbook', 'addSuccess'], false)
    }
    case DELETE_HANDBOOK_ITEM: {
      return state.setIn(['handbook', 'deleting'], true)
    }
    case DELETE_HANDBOOK_ITEM_COMPLETE: {
      return state.setIn(['handbook', 'deleting'], false)
    }
    case ADD_HANDBOOK_ITEM_COMPLETE:
    case EDIT_HANDBOOK_ITEM_COMPLETE: {
      return state.setIn(['handbook', 'adding'], false).setIn(['handbook', 'addSuccess'], action.payload || false)
    }
    case FETCH_HANDBOOK_ITEM: {
      return state.setIn(['handbook', 'loading'], true)
    }
    case FETCH_HANDBOOK_ITEM_COMPLETE: {
      {
        let data = {};
        (action.payload || []).forEach(item => {
          if (!!!data[`cate-${item.post_category_id}`]) {
            data[`cate-${item.post_category_id}`] = []
          }

          data[`cate-${item.post_category_id}`].push(item)
        })
        return state.setIn(['handbook', 'loading'], false)
          .setIn(['handbook', 'data'], data)
      }
    }
    default:
      return state;
  }
}

export default residentHandbookReducer;
