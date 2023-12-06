import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the paymentManagementClusterPage state domain
 */

const selectPaymentManagementClusterPageDomain = state => state.get("paymentManagementClusterPage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by PaymentManagementClusterPage
 */

const makeSelectPaymentManagementClusterPage = () =>
  createSelector(selectPaymentManagementClusterPageDomain, substate => substate.toJS());

export default makeSelectPaymentManagementClusterPage;
export { selectPaymentManagementClusterPageDomain };
