import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the dashboardBookingList state domain
 */

const selectDashboardBookingListDomain = state =>
  state.get("dashboardBookingList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by DashboardBookingList
 */

const makeSelectDashboardBookingList = () =>
  createSelector(selectDashboardBookingListDomain, substate => substate.toJS());

export default makeSelectDashboardBookingList;
export { selectDashboardBookingListDomain };
