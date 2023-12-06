import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the MaintainAdd state domain
 */

const selectMaintainAddDomain = (state) =>
  state.get("maintainAdd", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by MaintainAdd
 */

const makeSelectMaintainAdd = () =>
  createSelector(selectMaintainAddDomain, (substate) => substate.toJS());

export default makeSelectMaintainAdd;
export { selectMaintainAddDomain };
