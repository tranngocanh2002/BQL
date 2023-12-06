import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the dashboardBillDetail state domain
 */

const selectDashboardBillDetailDomain = state =>
  state.get("dashboardBillDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by DashboardBillDetail
 */

const makeSelectDashboardBillDetail = () =>
  createSelector(selectDashboardBillDetailDomain, substate => substate.toJS());

export default makeSelectDashboardBillDetail;
export { selectDashboardBillDetailDomain };
