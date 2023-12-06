import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the invoiceBillList state domain
 */

const selectInvoiceBillListDomain = state => state.get("invoiceBillList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by InvoiceBillList
 */

const makeSelectInvoiceBillList = () =>
  createSelector(selectInvoiceBillListDomain, substate => substate.toJS());

export default makeSelectInvoiceBillList;
export { selectInvoiceBillListDomain };
