import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the CancelBillList state domain
 */

const selectCancelBillListDomain = state => state.get("CancelBillList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by CancelBillList
 */

const makeSelectCancelBillList = () =>
  createSelector(selectCancelBillListDomain, substate => substate.toJS());

export default makeSelectCancelBillList;
export { selectCancelBillListDomain };
