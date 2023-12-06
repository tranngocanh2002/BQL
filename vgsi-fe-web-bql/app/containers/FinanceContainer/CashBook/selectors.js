import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the CashBook state domain
 */

const selectCashBookDomain = state => state.get("CashBook", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by CashBook
 */

const makeSelectCashBook = () =>
  createSelector(selectCashBookDomain, substate => substate.toJS());

export default makeSelectCashBook;
export { selectCashBookDomain };
