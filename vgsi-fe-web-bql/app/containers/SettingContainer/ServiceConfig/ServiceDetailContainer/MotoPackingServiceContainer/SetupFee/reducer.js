/*
 *
 * SetupFeeMotoPackingPage reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_SERVICE_PROVIDER, FETCH_SERVICE_PROVIDER_COMPLETE, UPDATE_SERVICE_DETAIL, UPDATE_SERVICE_DETAIL_COMPLETE, FETCH_FEE_LEVEL, FETCH_FEE_LEVEL_COMPLETE, CREATE_FEE_LEVEL, CREATE_FEE_LEVEL_COMPLETE, UPDATE_FEE_LEVEL, UPDATE_FEE_LEVEL_COMPLETE, DELETE_FEE_LEVEL } from "./constants";

export const initialState = fromJS({
  loading: false,
  items: [],
  updating: false,
  success: false,
});

function SetupFeeMotoPackingPageReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_FEE_LEVEL:
    case DELETE_FEE_LEVEL:
      return state.set('loading', true)
    case FETCH_FEE_LEVEL_COMPLETE:
      return state.set('loading', false).set('items', action.payload || []).set('updating', false)
    case CREATE_FEE_LEVEL:
    case UPDATE_FEE_LEVEL:
      {
        return state.set('updating', true).set('success', false)
      }
    case CREATE_FEE_LEVEL_COMPLETE: {
      return state.set('updating', false).set('success', !!action.payload || false).set('items', state.toJS().items.concat(!!action.payload ? [action.payload] : []))
    }
    case UPDATE_FEE_LEVEL_COMPLETE: {
      return state.set('updating', false).set('success', !!action.payload || false)
        .set('items', state.toJS().items.map(mm => {
          if (mm.id == action.payload.id) {
            return {
              ...action.payload
            }
          }
          return mm
        }))
    }
    default:
      return state;
  }
}

export default SetupFeeMotoPackingPageReducer;
