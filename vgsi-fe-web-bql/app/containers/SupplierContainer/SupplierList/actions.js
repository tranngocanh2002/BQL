/*
 *
 * ResidentList actions
 *
 */

import {
  DEFAULT_ACTION,
  DELETE_SUPPLIER,
  DELETE_SUPPLIER_COMPLETE,
  FETCH_ALL_SUPPLIER,
  FETCH_ALL_SUPPLIER_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchAllSupplierAction(payload) {
  return {
    type: FETCH_ALL_SUPPLIER,
    payload,
  };
}
export function fetchAllSupplierCompleteAction(payload) {
  return {
    type: FETCH_ALL_SUPPLIER_COMPLETE,
    payload,
  };
}

export function deleteSupplierAction(payload) {
  return {
    type: DELETE_SUPPLIER,
    payload,
  };
}
export function deleteSupplierCompleteAction(payload) {
  return {
    type: DELETE_SUPPLIER_COMPLETE,
    payload,
  };
}
