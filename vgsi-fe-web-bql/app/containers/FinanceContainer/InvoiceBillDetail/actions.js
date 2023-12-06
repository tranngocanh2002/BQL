/*
 *
 * InvoiceBillDetail actions
 *
 */

import {
  DEFAULT_ACTION, FETCH_DETAIL_BILL, FETCH_DETAIL_BILL_COMPLETE,
  DELETE_BILL, DELETE_BILL_COMPLETE,
  UPDATE_BILL, UPDATE_BILL_COMPLETE,
  UPDATE_BILL_STATUS, UPDATE_BILL_STATUS_COMPLETE
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}


export function fetchDetailBillAction(payload) {
  return {
    type: FETCH_DETAIL_BILL,
    payload
  };
}

export function fetchDetailBillCompleteAction(payload) {
  return {
    type: FETCH_DETAIL_BILL_COMPLETE,
    payload
  };
}

export function deleteDetailBillAction(payload) {
  return {
    type: DELETE_BILL,
    payload
  };
}

export function deleteDetailBillCompleteAction(payload) {
  return {
    type: DELETE_BILL_COMPLETE,
    payload
  };
}

export function updateDetailBillAction(payload) {
  return {
    type: UPDATE_BILL,
    payload
  };
}

export function updateDetailBillCompleteAction(payload) {
  return {
    type: UPDATE_BILL_COMPLETE,
    payload
  };
}

export function updateStatusBillAction(payload) {
  return {
    type: UPDATE_BILL_STATUS,
    payload
  };
}

export function updateStatusBillCompleteAction(payload) {
  return {
    type: UPDATE_BILL_STATUS_COMPLETE,
    payload
  };
}