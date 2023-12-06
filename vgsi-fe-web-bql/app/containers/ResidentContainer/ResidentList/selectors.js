import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the residentList state domain
 */

const selectResidentListDomain = state =>
  state.get("residentList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ResidentList
 */

const makeSelectResidentList = () =>
  createSelector(selectResidentListDomain, substate => substate.toJS());

export default makeSelectResidentList;
export { selectResidentListDomain };
