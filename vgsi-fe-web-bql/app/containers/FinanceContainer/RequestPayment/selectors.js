import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the requestPayment state domain
 */

const selectRequestPaymentDomain = state =>
  state.get("requestPayment", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by RequestPayment
 */

const makeSelectRequestPayment = () =>
  createSelector(selectRequestPaymentDomain, substate => substate.toJS());

export default makeSelectRequestPayment;
export { selectRequestPaymentDomain };
