import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the requestPaymentDetail state domain
 */

const selectRequestPaymentDetailDomain = (state) =>
  state.get("requestPaymentDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by RequestPaymentDetail
 */

const makeSelectRequestPaymentDetail = () =>
  createSelector(selectRequestPaymentDetailDomain, (substate) =>
    substate.toJS()
  );

export default makeSelectRequestPaymentDetail;
export { selectRequestPaymentDetailDomain };
