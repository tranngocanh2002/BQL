import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the staffList state domain
 */

const selectStaffListDomain = state => state.get("staffList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by StaffList
 */

const makeSelectStaffList = () =>
  createSelector(selectStaffListDomain, substate => substate.toJS());

export default makeSelectStaffList;
export { selectStaffListDomain };
