import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the dashboardReception state domain
 */

const selectDashboardReceptionDomain = state =>
  state.get("dashboardReception", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by DashboardReception
 */

const makeSelectDashboardReception = () =>
  createSelector(selectDashboardReceptionDomain, substate => substate.toJS());

export default makeSelectDashboardReception;
export { selectDashboardReceptionDomain };
