import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the serviceBookingFeeList state domain
 */

const selectServiceBookingFeeListDomain = state =>
  state.get("serviceBookingFeeList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ServiceBookingFeeList
 */

const makeSelectServiceBookingFeeList = () =>
  createSelector(selectServiceBookingFeeListDomain, substate => substate.toJS());

export default makeSelectServiceBookingFeeList;
export { selectServiceBookingFeeListDomain };
