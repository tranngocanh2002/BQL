import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the staffDetail state domain
 */

const selectStaffDetailDomain = (state) =>
  state.get("staffDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by StaffDetail
 */

const makeSelectStaffDetail = () =>
  createSelector(selectStaffDetailDomain, (substate) => substate.toJS());

export default makeSelectStaffDetail;
export { selectStaffDetailDomain };
