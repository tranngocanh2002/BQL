import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the residentGroup state domain
 */

const selectResidentGroupDomain = state =>
  state.get("residentGroup", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ResidentGroup
 */

const makeSelectResidentGroup = () =>
  createSelector(selectResidentGroupDomain, substate => substate.toJS());

export default makeSelectResidentGroup;
export { selectResidentGroupDomain };
