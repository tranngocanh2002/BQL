/*
 *
 * SettingPlane reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, CREATE_AREA, CREATE_AREA_COMPLETE, GET_BUILDING_AREA, GET_BUILDING_AREA_COMPLETE, UPDATE_AREA, UPDATE_AREA_COMPLETE, DELETE_AREA, DELETE_AREA_COMPLETE } from "./constants";

export const initialState = fromJS({
  creatingArea: false,
  updatingArea: false,
  deletingArea: false,
  buildingArea: {
    loading: true,
    lst: []
  }
});

function settingPlaneReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case UPDATE_AREA:
      return state.set('updatingArea', true)
    case UPDATE_AREA_COMPLETE:
      return state.set('updatingArea', false)
    case DELETE_AREA:
          return state.set('deletingArea', true)
    case DELETE_AREA_COMPLETE:
          return state.set('deletingArea', false)
    case CREATE_AREA:
      return state.set('creatingArea', true)
    case CREATE_AREA_COMPLETE:
      return state.set('creatingArea', false)
    case GET_BUILDING_AREA:
      return state.setIn(['buildingArea', 'loading'], true)
    case GET_BUILDING_AREA_COMPLETE:
      return state.setIn(['buildingArea', 'loading'], false)
        .setIn(['buildingArea', 'lst'], fromJS(action.payload || []))
    default:
      return state;
  }
}

export default settingPlaneReducer;
