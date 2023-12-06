import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the resourceManager state domain
 */

const selectResourceManagerDomain = state =>
  state.get("resourceManager", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ResourceManager
 */

const makeSelectResourceManager = () =>
  createSelector(selectResourceManagerDomain, substate => substate.toJS());

export default makeSelectResourceManager;
export { selectResourceManagerDomain };
