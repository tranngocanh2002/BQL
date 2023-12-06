import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the bookingUtilityPage state domain
 */

const selectBookingUtilityPageDomain = state =>
  state.get("bookingUtilityPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by BookingUtilityPage
 */

const makeSelectBookingUtilityPage = () =>
  createSelector(selectBookingUtilityPageDomain, substate => substate.toJS());

export default makeSelectBookingUtilityPage;
export { selectBookingUtilityPageDomain };
