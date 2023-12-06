import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the staffAdd state domain
 */

const selectStaffAddDomain = state => state.get("staffAdd", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by StaffAdd
 */

const makeSelectStaffAdd = () =>
  createSelector(selectStaffAddDomain, substate => substate.toJS());

export default makeSelectStaffAdd;
export { selectStaffAddDomain };
