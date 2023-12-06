import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the invoiceBillDetail state domain
 */

const selectInvoiceBillDetailDomain = state => state.get("invoiceBillDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by InvoiceBillDetail
 */

const makeSelectInvoiceBillDetail = () =>
  createSelector(selectInvoiceBillDetailDomain, substate => substate.toJS());

export default makeSelectInvoiceBillDetail;
export { selectInvoiceBillDetailDomain };
