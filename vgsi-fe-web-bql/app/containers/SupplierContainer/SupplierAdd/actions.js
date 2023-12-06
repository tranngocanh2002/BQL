/*
 *
 * SupplierAdd actions
 *
 */

import {
  CREATE_SUPPLIER,
  CREATE_SUPPLIER_COMPLETE,
  DEFAULT_ACTION,
  FETCH_SUPPLIER_DETAIL,
  FETCH_SUPPLIER_DETAIL_COMPLETE,
  UPDATE_SUPPLIER,
  UPDATE_SUPPLIER_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function createSupplierAction(payload) {
  return {
    type: CREATE_SUPPLIER,
    payload,
  };
}

export function createSupplierCompleteAction(payload) {
  return {
    type: CREATE_SUPPLIER_COMPLETE,
    payload,
  };
}

export function updateSupplierAction(payload) {
  return {
    type: UPDATE_SUPPLIER,
    payload,
  };
}

export function updateSupplierCompleteAction(payload) {
  return {
    type: UPDATE_SUPPLIER_COMPLETE,
    payload,
  };
}

export function fetchSupplierDetailAction(payload) {
  return {
    type: FETCH_SUPPLIER_DETAIL,
    payload,
  };
}

export function fetchSupplierDetailCompleteAction(payload) {
  return {
    type: FETCH_SUPPLIER_DETAIL_COMPLETE,
    payload,
  };
}
