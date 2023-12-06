import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the infoUsage state domain
 */

const selectInfoUsageDomain = state => state.get("infoUsageBuilding", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by InfoUsage
 */

const makeSelectInfoUsage = () =>
  createSelector(selectInfoUsageDomain, substate => substate.toJS());

export default makeSelectInfoUsage;
export { selectInfoUsageDomain };
