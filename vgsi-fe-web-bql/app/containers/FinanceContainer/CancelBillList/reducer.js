/*
 *
 * CancelBillList reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_ALL_BILL, FETCH_ALL_BILL_COMPLETE, FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE, FETCH_BUILDING_AREA, FETCH_BUILDING_AREA_COMPLETE } from "./constants";

export const initialState = fromJS({
	apartments: {
		loading: false,
		lst: []
	},
	loading: false,
	items: [],
	totalPage: 1,
	buildingArea: {
		loading: false,
		lst: []
	},
});

function CancelBillListReducer(state = initialState, action) {
	switch (action.type) {
		case DEFAULT_ACTION:
			return state;
		case FETCH_ALL_BILL:
			return state.set('loading', true)
		case FETCH_ALL_BILL_COMPLETE:
			let data = [];
			let totalPage = 1;

			if (!!action.payload) {
				data = action.payload.data
				totalPage = action.payload.totalPage
			}
			return state.set('loading', false)
				.set('items', fromJS(data)).set('totalPage', totalPage)
		case FETCH_APARTMENT:
			return state.setIn(['apartments', 'loading'], true)
		case FETCH_APARTMENT_COMPLETE:
			return state.setIn(['apartments', 'loading'], false)
				.setIn(['apartments', 'lst'], action.payload ? fromJS(action.payload) : -1)
		case FETCH_BUILDING_AREA: {
			return state.setIn(['buildingArea', 'loading'], true)
		}
		case FETCH_BUILDING_AREA_COMPLETE: {
			return state.setIn(['buildingArea', 'loading'], false)
				.setIn(['buildingArea', 'lst'], action.payload || [])
		}
		default:
			return state;
	}
}

export default CancelBillListReducer;
