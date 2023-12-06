import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the accountBase state domain
 */

const selectAccountBaseDomain = state => state.get("accountBase", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by AccountBase
 */

const makeSelectAccountBase = () =>
  createSelector(selectAccountBaseDomain, substate => substate.toJS());

export default makeSelectAccountBase;
export { selectAccountBaseDomain };
