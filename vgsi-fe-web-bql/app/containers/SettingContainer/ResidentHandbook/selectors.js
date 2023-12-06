import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the residentHandbook state domain
 */

const selectResidentHandbookDomain = state =>
  state.get("residentHandbook", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ResidentHandbook
 */

const makeSelectResidentHandbook = () =>
  createSelector(selectResidentHandbookDomain, substate => substate.toJS());

export default makeSelectResidentHandbook;
export { selectResidentHandbookDomain };
