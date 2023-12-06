import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the serviceProviderAdd state domain
 */

const selectServiceProviderAddDomain = state =>
  state.get("serviceProviderAdd", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ServiceProviderAdd
 */

const makeSelectServiceProviderAdd = () =>
  createSelector(selectServiceProviderAddDomain, substate => substate.toJS());

export default makeSelectServiceProviderAdd;
export { selectServiceProviderAddDomain };
