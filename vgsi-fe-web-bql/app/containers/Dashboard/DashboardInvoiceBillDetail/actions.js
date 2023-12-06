/*
 *
 * DashboardInvoiceBillDetail actions
 *
 */

import { DEFAULT_ACTION, FETCH_DETAIL_BILL_COMPLETE, CANCEL_BILL, CANCEL_BILL_COMPLETE, CHANGE_STATUS_BILL, CHANGE_STATUS_BILL_COMPLETE, UPDATE_BILL, UPDATE_BILL_COMPLETE, BLOCK_BILL, BLOCK_BILL_COMPLETE } from "./constants";
import { FETCH_DETAIL_BILL } from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION
  };
}

export function fetchDetailBill(payload) {
  return {
    type: FETCH_DETAIL_BILL,
    payload
  };
}

export function fetchDetailBillComplete(payload) {
  return {
    type: FETCH_DETAIL_BILL_COMPLETE,
    payload
  };
}

export function cancelBill(payload) {
  return {
    type: CANCEL_BILL,
    payload
  };
}

export function cancelBillComplete(payload) {
  return {
    type: CANCEL_BILL_COMPLETE,
    payload
  };
}

export function changeStatusBill(payload) {
  return {
    type: CHANGE_STATUS_BILL,
    payload
  };
}

export function changeStatusBillComplete(payload) {
  return {
    type: CHANGE_STATUS_BILL_COMPLETE,
    payload
  };
}

export function updateBill(payload) {
  return {
    type: UPDATE_BILL,
    payload
  };
}

export function updateBillComplete(payload) {
  return {
    type: UPDATE_BILL_COMPLETE,
    payload
  };
}

export function blockBill(payload) {
  return {
    type: BLOCK_BILL,
    payload
  };
}

export function blockBillComplete(payload) {
  return {
    type: BLOCK_BILL_COMPLETE,
    payload
  };
}