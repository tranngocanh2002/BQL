import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the billDetail state domain
 */

const selectBillDetailDomain = state => state.get("billDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by BillDetail
 */

const makeSelectBillDetail = () =>
  createSelector(selectBillDetailDomain, substate => substate.toJS());

export default makeSelectBillDetail;
export { selectBillDetailDomain };
