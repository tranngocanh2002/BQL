import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the dashboardBillDetail state domain
 */

const selectDashboardInvoiceBillDetailDomain = state =>
  state.get("dashboardInvoiceBillDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by DashboardInvoiceBillDetail
 */

const makeSelectDashboardInvoiceBillDetail = () =>
  createSelector(selectDashboardInvoiceBillDetailDomain, substate => substate.toJS());

export default makeSelectDashboardInvoiceBillDetail;
export { selectDashboardInvoiceBillDetailDomain };
