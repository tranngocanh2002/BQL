import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the bookingDetail state domain
 */

const selectBookingDetailDomain = state =>
  state.get("bookingDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by BookingDetail
 */

const makeSelectBookingDetail = () =>
  createSelector(selectBookingDetailDomain, substate => substate.toJS());

export default makeSelectBookingDetail;
export { selectBookingDetailDomain };
