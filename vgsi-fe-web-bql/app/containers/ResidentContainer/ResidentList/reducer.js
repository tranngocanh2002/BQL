/*
 *
 * ResidentList reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_ALL_RESIDENT, DELETE_RESIDENT, DELETE_RESIDENT_COMPLETE, FETCH_ALL_RESIDENT_COMPLETE, FETCH_APARTMENT_OF_RESIDENT, FETCH_APARTMENT_OF_RESIDENT_COMPLETE, IMPORT_RESIDENT, IMPORT_RESIDENT_COMPLETE } from "./constants";
import { UPDATE_DETAIL, UPDATE_DETAIL_COMPLETE } from "../ResidentDetail/constants";

export const initialState = fromJS({
  loading: false,
  totalPage: 1,
  data: [],
  deleting: false,
  updating: false,
  aparmentOfResident: {

  },
  importing: false
});

function residentListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_RESIDENT:
      return state.set('loading', true)
    case IMPORT_RESIDENT:
      return state.set('importing', true)
    case IMPORT_RESIDENT_COMPLETE:
      return state.set('importing', false)
    case DELETE_RESIDENT:
      return state.set('deleting', true)
    case DELETE_RESIDENT_COMPLETE:
      return state.set('deleting', false)
    case FETCH_ALL_RESIDENT_COMPLETE: {
      let data = [];
      let totalPage = 1;

      if (!!action.payload) {
        data = action.payload.data
        totalPage = action.payload.totalPage
      }

      return state.set('loading', false).set('deleting', false).
        set('data', fromJS(data)).set('totalPage', totalPage)
    }
    case UPDATE_DETAIL:
      return state.set('updating', true)
    case UPDATE_DETAIL_COMPLETE:
      return state.set('updating', false)
    case FETCH_APARTMENT_OF_RESIDENT: {
      const { resident_user_id } = action.payload
      let aparmentOfResident = { ...state.toJS().aparmentOfResident }
      aparmentOfResident[`resident_user_id-${resident_user_id}`] = {
        ...(aparmentOfResident[`resident_user_id-${resident_user_id}`] || { loading: false, lst: [] }),
        loading: true
      }
      return state.set('aparmentOfResident', aparmentOfResident)
    }
    case FETCH_APARTMENT_OF_RESIDENT_COMPLETE: {
      const { resident_user_id, lst } = action.payload
      let aparmentOfResident = { ...state.toJS().aparmentOfResident }
      aparmentOfResident[`resident_user_id-${resident_user_id}`] = {
        ...(aparmentOfResident[`resident_user_id-${resident_user_id}`] || { loading: false, lst: [] }),
        loading: false,
        lst
      }
      return state.set('aparmentOfResident', aparmentOfResident)
    }
    default:
      return state;
  }
}

export default residentListReducer;
