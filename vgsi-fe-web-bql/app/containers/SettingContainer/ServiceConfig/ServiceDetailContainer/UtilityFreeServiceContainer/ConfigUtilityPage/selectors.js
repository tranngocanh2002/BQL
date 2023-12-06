import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the configUtilityPage state domain
 */

const selectConfigUtilityPageDomain = state =>
  state.get("configUtilityPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ConfigUtilityPage
 */

const makeSelectConfigUtilityPage = () =>
  createSelector(selectConfigUtilityPageDomain, substate => substate.toJS());

export default makeSelectConfigUtilityPage;
export { selectConfigUtilityPageDomain };
