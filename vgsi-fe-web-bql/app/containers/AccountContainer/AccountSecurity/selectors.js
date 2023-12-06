import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the accountSecurity state domain
 */

const selectAccountSecurityDomain = state =>
  state.get("accountSecurity", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by AccountSecurity
 */

const makeSelectAccountSecurity = () =>
  createSelector(selectAccountSecurityDomain, substate => substate.toJS());

export default makeSelectAccountSecurity;
export { selectAccountSecurityDomain };
