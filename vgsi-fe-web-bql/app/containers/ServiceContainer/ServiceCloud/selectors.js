import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the serviceCloud state domain
 */

const selectServiceCloudDomain = state =>
  state.get("serviceCloud", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ServiceCloud
 */

const makeSelectServiceCloud = () =>
  createSelector(selectServiceCloudDomain, substate => substate.toJS());

export default makeSelectServiceCloud;
export { selectServiceCloudDomain };
