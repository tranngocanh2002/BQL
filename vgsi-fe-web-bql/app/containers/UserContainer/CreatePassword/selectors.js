import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the createPassword state domain
 */

const selectCreatePasswordDomain = state =>
  state.get("createPassword", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by CreatePassword
 */

const makeSelectCreatePassword = () =>
  createSelector(selectCreatePasswordDomain, substate => substate.toJS());

export default makeSelectCreatePassword;
export { selectCreatePasswordDomain };
