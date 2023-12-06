/*
 *
 * DashboardBillDetail reducer
 *
 */

import { fromJS } from "immutable";
import { DEFAULT_ACTION, FETCH_DETAIL_BILL, FETCH_DETAIL_BILL_COMPLETE, CANCEL_BILL, CANCEL_BILL_COMPLETE, CHANGE_STATUS_BILL, CHANGE_STATUS_BILL_COMPLETE, UPDATE_BILL, UPDATE_BILL_COMPLETE, BLOCK_BILL, BLOCK_BILL_COMPLETE } from "./constants";

export const initialState = fromJS({
  loading: true,
  updating: false,
  data: undefined,
  canceling: false,
  cancelSuccess: false,
  changingStatus: false,
  changingStatusSuccess: false,
  blocking: false
});

function dashboardBillDetailReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_DETAIL_BILL: {
      return state.set('loading', true)
    }
    case UPDATE_BILL: {
      return state.set('updating', true)
    }
    case UPDATE_BILL_COMPLETE: {
      if (!!action.payload)
        return state.set('updating', false).set('data', action.payload)
      return state.set('updating', false)
    }
    case FETCH_DETAIL_BILL_COMPLETE: {
      return state.set('loading', false).set('data', action.payload)
    }
    case CANCEL_BILL: {
      return state.set('canceling', true)
    }
    case CANCEL_BILL_COMPLETE: {
      let data = state.toJS().data
      if (action.payload) {
        data = {
          ...data,
          status: 2,
          status_name: "Đã hủy"
        }
      }
      return state.set('canceling', false)
        .set('cancelSuccess', action.payload)
        .set('data', data)
    }
    case CHANGE_STATUS_BILL: {
      return state.set('changingStatus', true)
    }
    case CHANGE_STATUS_BILL_COMPLETE: {
      let data = state.toJS().data
      if (action.payload) {
        data = {
          ...data,
          ...action.payload
        }
      }
      return state.set('changingStatus', false)
        .set('changingStatusSuccess', !!action.payload)
        .set('data', data)
    }
    case BLOCK_BILL: {
			return state.set('blocking', true)
		}
		case BLOCK_BILL_COMPLETE: {
			return state.set('blocking', false)
		}
    default:
      return state;
  }
}

export default dashboardBillDetailReducer;
