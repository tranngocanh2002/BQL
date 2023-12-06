import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the dashboardInvoiceBill state domain
 */

const selectDashboardInvoiceBillDomain = state =>
  state.get("dashboardInvoiceBill", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by DashboardInvoiceBill
 */

const makeSelectDashboardInvoiceBill = () =>
  createSelector(selectDashboardInvoiceBillDomain, substate => substate.toJS());

export default makeSelectDashboardInvoiceBill;
export { selectDashboardInvoiceBillDomain };
