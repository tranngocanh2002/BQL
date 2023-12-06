import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the paymentMotoPackingPage state domain
 */

const selectPaymentMotoPackingPageDomain = state => state.get("paymentMotoPackingPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by PaymentMotoPackingPage
 */

const makeSelectPaymentMotoPackingPage = () =>
  createSelector(selectPaymentMotoPackingPageDomain, substate => substate.toJS());

export default makeSelectPaymentMotoPackingPage;
export { selectPaymentMotoPackingPageDomain };
