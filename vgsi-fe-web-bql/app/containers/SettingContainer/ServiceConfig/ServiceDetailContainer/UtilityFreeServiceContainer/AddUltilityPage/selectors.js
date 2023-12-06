import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the addUltilityPage state domain
 */

const selectAddUltilityPageDomain = state =>
  state.get("addUltilityPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by AddUltilityPage
 */

const makeSelectAddUltilityPage = () =>
  createSelector(selectAddUltilityPageDomain, substate => substate.toJS());

export default makeSelectAddUltilityPage;
export { selectAddUltilityPageDomain };
