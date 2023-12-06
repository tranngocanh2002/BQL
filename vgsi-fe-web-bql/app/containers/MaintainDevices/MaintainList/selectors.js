import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the MaintainList state domain
 */

const selectMaintainListDomain = (state) =>
  state.get("maintainList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by MaintainList
 */

const makeSelectMaintainList = () =>
  createSelector(selectMaintainListDomain, (substate) => substate.toJS());

export default makeSelectMaintainList;
export { selectMaintainListDomain };
