import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the feeList state domain
 */

const selectFeeListDomain = state => state.get("feeListApartmentDetail", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by FeeList
 */

const makeSelectFeeList = () =>
  createSelector(selectFeeListDomain, substate => substate.toJS());

export default makeSelectFeeList;
export { selectFeeListDomain };
