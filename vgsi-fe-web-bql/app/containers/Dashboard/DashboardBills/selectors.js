import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the dashboardBills state domain
 */

const selectDashboardBillsDomain = state =>
  state.get("dashboardBills", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by DashboardBills
 */

const makeSelectDashboardBills = () =>
  createSelector(selectDashboardBillsDomain, substate => substate.toJS());

export default makeSelectDashboardBills;
export { selectDashboardBillsDomain };
