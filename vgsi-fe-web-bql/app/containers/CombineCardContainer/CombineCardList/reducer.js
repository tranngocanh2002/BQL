/*
 *
 * ApartmentList reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_COMBINE_CARD,
  DELETE_COMBINE_CARD,
  DELETE_COMBINE_CARD_COMPLETE,
  FETCH_ALL_COMBINE_CARD_COMPLETE,
  UPDATE_DETAIL,
  UPDATE_DETAIL_COMPLETE,
  IMPORT_COMBINE_CARD,
  IMPORT_COMBINE_CARD_COMPLETE,
  CREATE_COMBINE_CARD,
  CREATE_COMBINE_CARD_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  totalPage: 1,
  data: [],
  buildingArea: {
    loading: true,
    tree: [],
  },
  allResident: {
    loading: false,
    totalPage: 1,
    data: [],
  },
  updating: false,
  deleting: false,
  importing: false,
});

function combineCardListReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case IMPORT_COMBINE_CARD:
      return state.set("importing", true);
    case IMPORT_COMBINE_CARD_COMPLETE:
      return state.set("importing", false);
    case FETCH_ALL_COMBINE_CARD:
      return state.set("loading", true);
    case CREATE_COMBINE_CARD:
    case UPDATE_DETAIL:
      return state.set("updating", true);
    case CREATE_COMBINE_CARD_COMPLETE:
    case UPDATE_DETAIL_COMPLETE:
      return state.set("updating", false);
    case DELETE_COMBINE_CARD:
      return state.set("deleting", true);
    case DELETE_COMBINE_CARD_COMPLETE:
      return state.set("deleting", false);
    case FETCH_ALL_COMBINE_CARD_COMPLETE: {
      let data = [];
      let totalPage = 0;

      if (action.payload) {
        data = action.payload.data;
        totalPage = action.payload.totalPage;
      }

      return state
        .set("loading", false)
        .set("deleting", false)
        .set("data", fromJS(data))
        .set("totalPage", totalPage);
    }
    default:
      return state;
  }
}

export default combineCardListReducer;
