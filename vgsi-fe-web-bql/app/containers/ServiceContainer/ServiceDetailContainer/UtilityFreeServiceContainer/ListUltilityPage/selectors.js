import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the listUlilityPage state domain
 */

const selectListUltilityPageDomain = state => state.get("listUlilityPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ListUltilityPage
 */

const makeSelectListUltilityPage = () =>
  createSelector(selectListUltilityPageDomain, substate => substate.toJS());

export default makeSelectListUltilityPage;
export { selectListUltilityPageDomain };
