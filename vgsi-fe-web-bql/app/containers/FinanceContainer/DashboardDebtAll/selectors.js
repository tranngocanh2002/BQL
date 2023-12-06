import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the dashboardDebtAll state domain
 */

const selectDashboardDebtAllDomain = state =>
  state.get("dashboardDebtAll", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by DashboardDebtAll
 */

const makeSelectDashboardDebtAll = () =>
  createSelector(selectDashboardDebtAllDomain, substate => substate.toJS());

export default makeSelectDashboardDebtAll;
export { selectDashboardDebtAllDomain };
