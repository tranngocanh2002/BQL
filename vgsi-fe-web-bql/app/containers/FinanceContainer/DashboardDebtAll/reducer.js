/*
 *
 * DashboardDebtAll reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_BUILDING_AREA, FETCH_BUILDING_AREA_COMPLETE, FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE, FETCH_DEBT, FETCH_DEBT_COMPLETE } from "./constants";

export const initialState = fromJS({
  apartments: {
    loading: false,
    lst: []
  },
  buildingArea: {
    loading: false,
    lst: []
  },
  loading: true,
  data: []
});

function dashboardDebtAllReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_BUILDING_AREA: {
      return state.setIn(['buildingArea', 'loading'], true)
    }
    case FETCH_BUILDING_AREA_COMPLETE: {
      return state.setIn(['buildingArea', 'loading'], false).setIn(['buildingArea', 'lst'], action.payload || [])
    }
    case FETCH_APARTMENT:
      return state.setIn(['apartments', 'loading'], true);
    case FETCH_APARTMENT_COMPLETE:
      return state.setIn(['apartments', 'loading'], false)
        .setIn(['apartments', 'lst'], action.payload || [])
    case FETCH_DEBT:
      return state.set('loading', true)
    case FETCH_DEBT_COMPLETE:
      let data = [];
      let totalPage = 1;
      let total_count = undefined

      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
        total_count = action.payload.total_count
      }
      return state.set('loading', false).set('items', fromJS(data)).set('totalPage', totalPage).set('total_count', total_count);
    default:
      return state;
  }
}

export default dashboardDebtAllReducer;
