import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the residentAdd state domain
 */

const selectResidentAddDomain = state => state.get("residentAdd", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ResidentAdd
 */

const makeSelectResidentAdd = () =>
  createSelector(selectResidentAddDomain, substate => substate.toJS());

export default makeSelectResidentAdd;
export { selectResidentAddDomain };
