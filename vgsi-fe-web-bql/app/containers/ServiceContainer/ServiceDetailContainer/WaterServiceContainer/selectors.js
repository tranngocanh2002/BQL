import { createSelector } from "reselect";
import { initialState } from "./reducer";

/**
 * Direct selector to the waterServiceContainer state domain
 */

const selectWaterServiceContainerDomain = state =>
  state.get("waterServiceContainer", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by WaterServiceContainer
 */

const makeSelectWaterServiceContainer = () =>
  createSelector(selectWaterServiceContainerDomain, substate =>
    substate.toJS()
  );

export default makeSelectWaterServiceContainer;
export { selectWaterServiceContainerDomain };
