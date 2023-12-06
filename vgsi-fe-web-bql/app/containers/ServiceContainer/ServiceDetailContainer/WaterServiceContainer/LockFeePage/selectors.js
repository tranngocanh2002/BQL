import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the lockFeePagePage state domain
 */

const selectLockFeePagePageDomain = state => state.get("lockFeePagePage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by LockFeePagePage
 */

const makeSelectLockFeePagePage = () =>
  createSelector(selectLockFeePagePageDomain, substate => substate.toJS());

export default makeSelectLockFeePagePage;
export { selectLockFeePagePageDomain };
