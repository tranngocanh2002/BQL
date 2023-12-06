import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the managementClusterServiceContainer state domain
 */

const selectManagementClusterServiceContainerDomain = state =>
  state.get("managementClusterServiceContainer", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by ManagementClusterServiceContainer
 */

const makeSelectManagementClusterServiceContainer = () =>
  createSelector(selectManagementClusterServiceContainerDomain, substate =>
    substate.toJS()
  );

export default makeSelectManagementClusterServiceContainer;
export { selectManagementClusterServiceContainerDomain };
