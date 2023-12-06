import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the dashboardDebt state domain
 */

const selectDashboardDebtDomain = state =>
  state.get("dashboardDebt", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by DashboardDebt
 */

const makeSelectDashboardDebt = () =>
  createSelector(selectDashboardDebtDomain, substate => substate.toJS());

export default makeSelectDashboardDebt;
export { selectDashboardDebtDomain };
