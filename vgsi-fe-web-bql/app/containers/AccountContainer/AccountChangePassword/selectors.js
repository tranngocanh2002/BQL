import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the accountChangePassword state domain
 */

const selectAccountChangePasswordDomain = state =>
  state.get("accountChangePassword", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by AccountChangePassword
 */

const makeSelectAccountChangePassword = () =>
  createSelector(selectAccountChangePasswordDomain, substate =>
    substate.toJS()
  );

export default makeSelectAccountChangePassword;
export { selectAccountChangePasswordDomain };
