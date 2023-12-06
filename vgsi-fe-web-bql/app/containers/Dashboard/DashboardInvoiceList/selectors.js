import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the dashboardInvoiceList state domain
 */

const selectDashboardInvoiceListDomain = state =>
  state.get("dashboardInvoiceList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by DashboardInvoiceList
 */

const makeSelectDashboardInvoiceList = () =>
  createSelector(selectDashboardInvoiceListDomain, substate => substate.toJS());

export default makeSelectDashboardInvoiceList;
export { selectDashboardInvoiceListDomain };
