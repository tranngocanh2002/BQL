import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the register state domain
 */

const selectVerifyOTPDomain = (state) => state.get("verifyOTP", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by VerifyOTP
 */

const makeSelectVerifyOTP = () =>
  createSelector(selectVerifyOTPDomain, (substate) => substate.toJS());

export default makeSelectVerifyOTP;
export { selectVerifyOTPDomain };
