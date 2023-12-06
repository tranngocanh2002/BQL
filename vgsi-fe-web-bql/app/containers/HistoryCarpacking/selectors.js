import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the HistoryCarpacking state domain
 */

const selectHistoryCarpackingDomain = state =>
  state.get("HistoryCarpacking", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by HistoryCarpacking
 */

const makeSelectHistoryCarpacking = () =>
  createSelector(selectHistoryCarpackingDomain, substate => substate.toJS());

export default makeSelectHistoryCarpacking;
export { selectHistoryCarpackingDomain };
