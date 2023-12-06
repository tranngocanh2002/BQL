import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the vihicleManagement state domain
 */

const selectVihicleManagementDomain = state =>
  state.get("vihicleManagement", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by VihicleManagement
 */

const makeSelectVihicleManagement = () =>
  createSelector(selectVihicleManagementDomain, substate => substate.toJS());

export default makeSelectVihicleManagement;
export { selectVihicleManagementDomain };
