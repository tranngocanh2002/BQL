/*
 *
 * ApartmentAdd actions
 *
 */

import {
  CREATE_EQUIPMENT,
  CREATE_EQUIPMENT_COMPLETE,
  DEFAULT_ACTION,
  UPDATE_EQUIPMENT,
  UPDATE_EQUIPMENT_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}
export function createEquipmentAction(payload) {
  return {
    type: CREATE_EQUIPMENT,
    payload,
  };
}

export function createEquipmentCompleteAction(payload) {
  return {
    type: CREATE_EQUIPMENT_COMPLETE,
    payload,
  };
}
export function updateEquipmentAction(payload) {
  return {
    type: UPDATE_EQUIPMENT,
    payload,
  };
}

export function updateEquipmentCompleteAction(payload) {
  return {
    type: UPDATE_EQUIPMENT_COMPLETE,
    payload,
  };
}
