/*
 *
 * LucidList reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_ALL_LUCID, FETCH_ALL_LUCID_COMPLETE, FETCH_ALL_RESIDENT, FETCH_ALL_RESIDENT_COMPLETE, FETCH_VEHICLE, FETCH_VEHICLE_COMPLETE, FETCH_ALL_APARTMENT, FETCH_ALL_APARTMENT_COMPLETE } from "./constants";

export const initialState = fromJS({
  loading: false,
  totalPage: 1,
  data: [],
  deleting: false,
  updating: false,
  resident: {
    loading: false,
    lst: []
  },
  apartment: {
    loading: false,
    lst: []
  },
  vehicle: {
    loading: false,
    lst: []
  }
});

function lucidListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_LUCID:
      return state.set('loading', true)
    case FETCH_ALL_LUCID_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
      }

      return state.set('loading', false).set('deleting', false).
        set('data', fromJS(data)).set('totalPage', totalPage)
    }
    case FETCH_VEHICLE: {
      return state.setIn(['vehicle', 'loading'], true)
    }
    case FETCH_VEHICLE_COMPLETE: {
      return state.setIn(['vehicle', 'loading'], false)
        .setIn(['vehicle', 'lst'], action.payload || [])
    }
    case FETCH_ALL_RESIDENT: {
      return state.setIn(['resident', 'loading'], true)
    }
    case FETCH_ALL_RESIDENT_COMPLETE: {
      return state.setIn(['resident', 'loading'], false)
        .setIn(['resident', 'lst'], action.payload || [])
    }
    case FETCH_ALL_APARTMENT: {
      return state.setIn(['apartment', 'loading'], true)
    }
    case FETCH_ALL_APARTMENT_COMPLETE: {
      return state.setIn(['apartment', 'loading'], false)
        .setIn(['apartment', 'lst'], action.payload || [])
    }
    default:
      return state;
  }
}

export default lucidListReducer;
