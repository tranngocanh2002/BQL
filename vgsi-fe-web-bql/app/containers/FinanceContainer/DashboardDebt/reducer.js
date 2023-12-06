/*
 *
 * DashboardDebt reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_ALL_BUILDING_AREA, FETCH_ALL_BUILDING_AREA_COMPLETE, FETCH_DETAIL_DEBT_AREA, FETCH_DETAIL_DEBT_AREA_COMPLETE } from "./constants";

export const initialState = fromJS({
  buildingArea: {
    loading: true,
    data: []
  },
  currentAreaSelected: undefined,
  loading: true,
  data: [],
  numberHasDebt: 0,
  numberHasNotDebt: 0,
});

function dashboardDebtReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_BUILDING_AREA:
      return state.setIn(['buildingArea', 'loading'], true);
    case FETCH_ALL_BUILDING_AREA_COMPLETE:
      return state.setIn(['buildingArea', 'loading'], false).setIn(['buildingArea', 'data'], action.payload);
    case FETCH_DETAIL_DEBT_AREA:
      return state.set('currentAreaSelected', action.payload.building_area).set('loading', true)
    // case FETCH_DETAIL_DEBT_AREA_COMPLETE: {
    //   let currentAreaSelected = state.toJS().currentAreaSelected || { floors: [] };
    //   currentAreaSelected = {
    //     ...currentAreaSelected,
    //     floors: currentAreaSelected.floors.map(fff => {
    //       return {
    //         ...fff,
    //         apartments: action.payload.filter(rrr => rrr.apartment_building_area_id == fff.id)
    //       }
    //     })
    //   }

    //   return state.set('currentAreaSelected', currentAreaSelected).set('loading', false)
    //     .set('numberHasDebt', action.payload.filter(rrr => rrr.total_debt > 0).length)
    //     .set('numberHasNotDebt', action.payload.filter(rrr => rrr.total_debt == 0).length)

    // }

    case FETCH_DETAIL_DEBT_AREA_COMPLETE:
      let data = [];
      let totalPage = 1;
      let total_count = undefined


      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
        total_count = action.payload.total_count
      }

      let currentAreaSelected = state.toJS().currentAreaSelected || { floors: [] };
      currentAreaSelected = {
        ...currentAreaSelected,
        floors: currentAreaSelected.floors.map(fff => {
          return {
            ...fff,
            apartments: data.filter(rrr => rrr.building_area_id == fff.id)
          }
        })
      }
      return state.set('currentAreaSelected', currentAreaSelected).set('loading', false).set('total_count', total_count)

    default:
      return state;
  }
}

export default dashboardDebtReducer;
