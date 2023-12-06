import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the CancelInvoiceBillList state domain
 */

const selectCancelInvoiceBillListDomain = state => state.get("CancelInvoiceBillList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by CancelInvoiceBillList
 */

const makeSelectCancelInvoiceBillList = () =>
  createSelector(selectCancelInvoiceBillListDomain, substate => substate.toJS());

export default makeSelectCancelInvoiceBillList;
export { selectCancelInvoiceBillListDomain };
