import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the bookingAdd state domain
 */

const selectBookingAddDomain = state =>
  state.get("bookingAdd", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by BookingAdd
 */

const makeSelectBookingAdd = () =>
  createSelector(selectBookingAddDomain, substate => substate.toJS());

export default makeSelectBookingAdd;
export { selectBookingAddDomain };
