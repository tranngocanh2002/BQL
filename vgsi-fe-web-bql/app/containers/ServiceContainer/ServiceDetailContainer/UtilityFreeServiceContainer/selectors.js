import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the utilityFreeServiceContainer state domain
 */

const selectUtilityFreeServiceContainerDomain = state =>
  state.get("utilityFreeServiceContainer", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by UtilityFreeServiceContainer
 */

const makeSelectUtilityFreeServiceContainer = () =>
  createSelector(selectUtilityFreeServiceContainerDomain, substate =>
    substate.toJS()
  );

export default makeSelectUtilityFreeServiceContainer;
export { selectUtilityFreeServiceContainerDomain };
