/*
 *
 * FeeList actions
 *
 */

import {
  DEFAULT_ACTION, FETCH_ALL_FEE, FETCH_ALL_FEE_COMPLETE,
  FETCH_SERVICE_MAP, FETCH_SERVICE_MAP_COMPLETE,
  DELETE_FEE, DELETE_FEE_COMPLETE, UPDATE_PAYMENT,
  UPDATE_PAYMENT_COMPLETE
} from "./constants";


export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}
export function fetchAllFee(payload) {
  return {
    type: FETCH_ALL_FEE,
    payload
  };
}

export function fetchAllFeeComplete(payload) {
  return {
    type: FETCH_ALL_FEE_COMPLETE,
    payload
  };
}

export function fetchServiceMapAction(payload){
  return {
    type: FETCH_SERVICE_MAP,
    payload
  };
}

export function fetchServiceMapCompleteAction(payload){
  return {
    type: FETCH_SERVICE_MAP_COMPLETE,
    payload
  };
}

export function deleteFeeAction(payload){
	return {
		type: DELETE_FEE,
		payload
	};
}

export function deleteFeeCompleteAction(payload){
	return {
		type: DELETE_FEE_COMPLETE,
		payload
	};
}

export function updatePayment(payload) {
  return {
    type: UPDATE_PAYMENT,
    payload
  };
}

export function updatePaymentComplete(payload) {
  return {
    type: UPDATE_PAYMENT_COMPLETE,
    payload
  };
}