/*
 *
 * ApartmentAdd reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_BUILDING_AREA, FETCH_BUILDING_AREA_COMPLETE, CREATE_APARTMENT, CREATE_APARTMENT_COMPLETE, FETCH_DETAIL_APARTMENT, FETCH_DETAIL_APARTMENT_COMPLETE, FETCH_ALL_APARTMENT_TYPE, FETCH_ALL_APARTMENT_TYPE_COMPLETE, FETCH_ALL_RESIDENT_BY_PHONE, FETCH_ALL_RESIDENT_BY_PHONE_COMPLETE } from "./constants";

export const initialState = fromJS({
  creating: false,
  success: false,
  updating: false,
  updateSuccess: false,
  detail: {
    loading: false,
    data: undefined
  },

  buildingArea: {
    loading: true,
    tree: []
  },
  apartment_type: {
    loading: false,
    data: []
  },
  allResident: {
    loading: false,
    totalPage: 1,
    data: [],
  },
});

function apartmentAddReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case CREATE_APARTMENT:
      return state.set('creating', true)
    case CREATE_APARTMENT_COMPLETE:
      return state.set('creating', false).set('success', action.payload || false)
    case FETCH_BUILDING_AREA:
      return state.setIn(['buildingArea', 'loading'], true)
    case FETCH_BUILDING_AREA_COMPLETE:
      return state.setIn(['buildingArea', 'loading'], false).setIn(['buildingArea', 'tree'], fromJS(action.payload || []))
    case FETCH_DETAIL_APARTMENT:
      return state.setIn(['detail', 'loading'], true)
    case FETCH_DETAIL_APARTMENT_COMPLETE:
      return state.setIn(['detail', 'loading'], false)
        .setIn(['detail', 'data'], action.payload ? fromJS(action.payload) : -1)
    case FETCH_ALL_APARTMENT_TYPE: {
      return state.setIn(['apartment_type', 'loading'], true)
    }
    case FETCH_ALL_APARTMENT_TYPE_COMPLETE: {
      return state.setIn(['apartment_type', 'loading'], false).setIn(['apartment_type', 'data'], fromJS(action.payload || []))
    }
    case FETCH_ALL_RESIDENT_BY_PHONE:
      return state.setIn(['allResident','loading'], true)
    case FETCH_ALL_RESIDENT_BY_PHONE_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
      }
      return state.setIn(['allResident','loading'], false).
        setIn(['allResident','data'], fromJS(data)).setIn(['allResident','totalPage'], totalPage)
    }
    default:
      return state;
  }
}

export default apartmentAddReducer;
