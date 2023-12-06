import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the residentOldList state domain
 */

const selectResidentOldListDomain = state =>
  state.get("residentOldList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ResidentOldList
 */

const makeSelectResidentOldList = () =>
  createSelector(selectResidentOldListDomain, substate => substate.toJS());

export default makeSelectResidentOldList;
export { selectResidentOldListDomain };
