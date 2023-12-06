/*
 *
 * ApartmentAdd actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_DETAIL_EQUIPMENT,
  FETCH_DETAIL_EQUIPMENT_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchEquipmentDetailAction(payload) {
  return {
    type: FETCH_DETAIL_EQUIPMENT,
    payload,
  };
}
export function fetchEquipmentDetailCompleteAction(payload) {
  return {
    type: FETCH_DETAIL_EQUIPMENT_COMPLETE,
    payload,
  };
}
