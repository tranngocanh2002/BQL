import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the lockFeePage state domain
 */

const selectLockFeePageDomain = state => state.get("lockFeePage", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by LockFeePage
 */

const makeSelectLockFeePage = () =>
  createSelector(selectLockFeePageDomain, substate => substate.toJS());

export default makeSelectLockFeePage;
export { selectLockFeePageDomain };
