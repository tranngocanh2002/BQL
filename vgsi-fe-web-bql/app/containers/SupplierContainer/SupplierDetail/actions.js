/*
 *
 * ResidentDetail actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_SUPPLIER_DETAIL,
  FETCH_SUPPLIER_DETAIL_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
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
