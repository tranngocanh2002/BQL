import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the DetailUltilityPage state domain
 */

const selectDetailUltilityPageDomain = state =>
  state.get("DetailUltilityPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by DetailUltilityPage
 */

const makeSelectDetailUltilityPage = () =>
  createSelector(selectDetailUltilityPageDomain, substate => substate.toJS());

export default makeSelectDetailUltilityPage;
export { selectDetailUltilityPageDomain };
