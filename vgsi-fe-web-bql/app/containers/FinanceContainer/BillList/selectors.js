import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the billList state domain
 */

const selectBillListDomain = state => state.get("billList", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by BillList
 */

const makeSelectBillList = () =>
  createSelector(selectBillListDomain, substate => substate.toJS());

export default makeSelectBillList;
export { selectBillListDomain };
