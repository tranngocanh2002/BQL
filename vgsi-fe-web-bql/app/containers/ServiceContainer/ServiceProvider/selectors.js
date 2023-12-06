import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the serviceProvider state domain
 */

const selectServiceProviderDomain = state =>
  state.get("serviceProvider", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ServiceProvider
 */

const makeSelectServiceProvider = () =>
  createSelector(selectServiceProviderDomain, substate => substate.toJS());

export default makeSelectServiceProvider;
export { selectServiceProviderDomain };
