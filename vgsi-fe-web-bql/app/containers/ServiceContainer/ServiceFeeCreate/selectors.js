import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the serviceFeeCreate state domain
 */

const selectServiceFeeCreateDomain = state =>
  state.get("serviceFeeCreate", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ServiceFeeCreate
 */

const makeSelectServiceFeeCreate = () =>
  createSelector(selectServiceFeeCreateDomain, substate => substate.toJS());

export default makeSelectServiceFeeCreate;
export { selectServiceFeeCreateDomain };
